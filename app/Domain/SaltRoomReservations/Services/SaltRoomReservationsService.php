<?php

namespace Domain\SaltRoomReservations\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Domain\SaltRoomReservations\Actions\CreateSaltRoomReservation;
use Domain\SaltRoomReservations\Actions\DeleteSaltRoomReservation;
use Domain\SaltRoomReservations\Actions\MarkAsUsedSaltRoomReservation;
use Domain\SaltRoomReservations\Actions\UpdateSaltRoomReservation;
use Domain\SaltRoomReservations\Contracts\Repositories\SaltRoomReservationOrderDetailsRepository;
use Domain\SaltRoomReservations\Contracts\Repositories\SaltRoomReservationsRepository;
use Domain\SaltRoomReservations\DataTransferObjects\SaltRoomReservationEntitiesCollection;
use Domain\SaltRoomReservations\DataTransferObjects\SaltRoomReservationEntity;
use Domain\SaltRoomReservations\DataTransferObjects\SaltRoomReservationSchedulesPdfResponse;
use Domain\SaltRoomReservations\DataTransferObjects\SaltRoomReservationSearchRequest;
use Domain\SaltRoomReservations\DataTransferObjects\SaltRoomReservationSearchResponse;
use Domain\SaltRoomReservations\DataTransferObjects\SaltRoomReservationSendUpcomingReservationEmailResponse;
use Domain\SaltRoomReservations\Mails\MailReservation;
use Domain\SaltRoomReservations\Mails\UpcomingReservation;
use Domain\SaltRoomReservations\Models\SaltRoomReservation;
use Domain\SaltRoomReservations\Transformers\SaltRoomReservationTransformer;
use Domain\Clients\Contracts\Services\ClientsService;
use Domain\Festives\Contracts\Services\FestivesService;
use Domain\Festives\DataTransferObjects\FestiveSearchRequest;
use Domain\Orders\Contracts\Services\OrdersService;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;
use Support\Core\Enums\SQLSort;
use Support\Core\Serializers\FractalDataArraySerializer;
use Support\DataTransferObjects\Contracts\Response;
use Support\Exceptions\InvalidDataTypeException;
use Support\Exceptions\InvalidStatusException;
use Support\Helpers\DatesHelper;

class SaltRoomReservationsService implements \Domain\SaltRoomReservations\Contracts\Services\SaltRoomReservationsService
{
    /**
     * @param SaltRoomReservationsRepository $repository
     * @param SaltRoomReservationOrderDetailsRepository $saltRoomReservationOrderDetailsRepository
     * @param ClientsService $clientsService
     * @param OrdersService $ordersService
     */
    public function __construct(
        protected readonly SaltRoomReservationsRepository $repository,
        protected readonly SaltRoomReservationOrderDetailsRepository $saltRoomReservationOrderDetailsRepository,
        protected readonly ClientsService $clientsService,
        protected readonly OrdersService $ordersService
    ) {
    }

    /**
     * @param array $data
     * @return SaltRoomReservationEntity
     * @throws UnknownProperties
     * @throws InvalidStatusException
     */
    public function create(array $data): SaltRoomReservationEntity
    {
        $record = app(CreateSaltRoomReservation::class)($data);
        $this->isNotifyEmail($data, $record);
        return $this->DTOFromModel($record);
    }

    /**
     * @param array $data
     * @return SaltRoomReservationEntity
     * @throws UnknownProperties
     */
    public function delete(array $data): SaltRoomReservationEntity
    {
        $record = app(DeleteSaltRoomReservation::class)($data);

        return $this->DTOFromModel($record);
    }

    /**
     * @param int $id
     * @param array $includes
     * @return SaltRoomReservationEntity|null
     * @throws UnknownProperties
     */
    public function find(int $id, array $includes = []): ?SaltRoomReservationEntity
    {
        if (!$record = $this->repository->find($id)) {
            return null;
        }

        return $this->DTOFromModel($record, $includes);
    }

    /**
     * @param int $id
     * @param array $includes
     * @return SaltRoomReservationSearchResponse
     * @throws InvalidDataTypeException
     */
    public function findByOrderDetail(int $id, array $includes = []): SaltRoomReservationSearchResponse
    {
        $ids = $this->saltRoomReservationOrderDetailsRepository->getEntity()->query()
            ->where('order_detail_id', $id)
            ->get()
            ->pluck('id')
            ->toArray();

        if (!count($ids)) {
            $ids = ['0'];
        }

        $query = $this->repository->searchQueryBuilder(['id' => $ids], 'id', SQLSort::SORT_ASC);
        $records = $query->paginate(config('system.infinite_pagination'));
        $collection = new Collection($records->items(), app(SaltRoomReservationTransformer::class), 'data');
        $collection->setPaginator(new IlluminatePaginatorAdapter($records));
        $manager = app(Manager::class);
        $manager->parseIncludes($includes);
        $manager->setSerializer(new FractalDataArraySerializer());
        $resourceCollection = $manager->createData($collection);

        $result = $resourceCollection->toArray();

        return (new SaltRoomReservationSearchResponse('Ok'))->setData(
            SaltRoomReservationEntitiesCollection::createFromArray($result['data'])
        )->setMeta($result['meta']);
    }

    /**
     * @param array $data
     * @return SaltRoomReservationEntity
     * @throws UnknownProperties
     */
    public function markAsUsed(array $data): SaltRoomReservationEntity
    {
        $record = app(MarkAsUsedSaltRoomReservation::class)($data);

        return $this->DTOFromModel($record);
    }

    /**
     * @param string $date
     * @return SaltRoomReservationSchedulesPdfResponse
     * @throws InvalidDataTypeException
     * @throws UnknownProperties
     * @throws InvalidStatusException
     */
    public function schedulesPdf(string $date): SaltRoomReservationSchedulesPdfResponse
    {
        $searchRequest = app(SaltRoomReservationSearchRequest::class, [
            'args' => [
                'filters' => ['date' => $date],
                'includes' => ['client', 'orderDetails', 'orderDetails.product', 'orderDetails.product.productType', 'orderDetails.order'],
                'paginateSize' => config('system.infinite_pagination'),
                'sortField' => 'time',
                'sortType' => SQLSort::SORT_ASC
            ]
        ]);

        $festiveRecords = app(FestivesService::class)->search(
            new FestiveSearchRequest([
                'filters' => [
                    'date' => $date,
                    'working_hours_as_sunday' => '1'
                ],
                'paginateSize' => config('system.infinite_pagination')
            ])
        )->getData();

        $date = Carbon::parse($date);
        $dayOfWeek = $date->dayOfWeek;
        if ($festiveRecords->count() > 0 && $festiveRecords->first()->working_hours_as_sunday) {
            $dayOfWeek = 0;
        }

        $records = $this->search($searchRequest);
        $schedules = collect(DatesHelper::dateSaltRoomSchedules($date))->chunk(5);

        $pdf = Pdf::loadView('pdf.salt_room_reservations_schedules', [
            'data' => [
                'records' => $records->getData()->sortBy(fn ($item) => strtotime($item->time)),
                'schedules' => $schedules,
                'dateNormal' => $date,
                'dayOfWeek' => $dayOfWeek,
                'date' => DatesHelper::spanishWeekDay(
                    $date->weekday()
                ) . ', ' . $date->day . ' de ' . DatesHelper::spanishMonthName($date->month) . ' del ' . $date->year
            ]
        ]);

        return (new SaltRoomReservationSchedulesPdfResponse(Response::STATUSES['OK']))
            ->setSuccess()
            ->setData(
                [
                    'title' => 'Reservas - ' . $date,
                    'content' => base64_encode($pdf->save('test.pdf')->output())
                ]
            );
    }

    /**
     * @param SaltRoomReservationSearchRequest $request
     *
     * @return SaltRoomReservationSearchResponse
     * @throws UnknownProperties
     * @throws InvalidDataTypeException
     */
    public function search(SaltRoomReservationSearchRequest $request): SaltRoomReservationSearchResponse
    {
        $query = $this->repository->searchQueryBuilder($request->filters, $request->sortField, $request->sortType);
        $records = $query->paginate($request->paginateSize);
        $collection = new Collection($records->items(), app(SaltRoomReservationTransformer::class), 'data');
        $collection->setPaginator(new IlluminatePaginatorAdapter($records));
        $manager = app(Manager::class);
        $manager->parseIncludes($request->includes);
        $manager->setSerializer(new FractalDataArraySerializer());
        $resourceCollection = $manager->createData($collection);

        $result = $resourceCollection->toArray();

        return (new SaltRoomReservationSearchResponse('Ok'))->setData(
            SaltRoomReservationEntitiesCollection::createFromArray($result['data'])
        )->setMeta($result['meta']);
    }

    /**
     * @param int $id string $email
     */
    public function sendUpcomingReservationEmail(int $id): SaltRoomReservationSendUpcomingReservationEmailResponse
    {
        $response = app(SaltRoomReservationSendUpcomingReservationEmailResponse::class, ['status' => 'Ok']);

        try {
            Mail::send(
                new UpcomingReservation($id, $this)
            );
            $response->setSuccess();
        } catch (Exception $ex) {
            logger($ex);
            $response->setErrors([$ex->getMessage()]);
        }

        return $response;
    }

    /**
     * @param array $data
     * @return SaltRoomReservationEntity
     * @throws UnknownProperties
     * @throws InvalidStatusException
     */
    public function update(array $data): SaltRoomReservationEntity
    {
        $record = app(UpdateSaltRoomReservation::class)($data);
        $this->isNotifyEmail($data, $record);
        return $this->DTOFromModel($record);
    }

    /**
     * @param SaltRoomReservation $entity
     * @param array $includes
     * @return SaltRoomReservationEntity
     * @throws UnknownProperties
     */
    private function DTOFromModel(SaltRoomReservation $entity, array $includes = []): SaltRoomReservationEntity
    {
        $manager = app(Manager::class);
        $manager->parseIncludes($includes);
        $manager->setSerializer(new FractalDataArraySerializer());
        $item = new Item($entity, app(SaltRoomReservationTransformer::class));
        $data = $manager->createData($item)->toArray();

        return new SaltRoomReservationEntity($data);
    }

    /**
     * @param array $data
     * @return SaltRoomReservationSendUpcomingReservationEmailResponse
     * @throws InvalidStatusException
     */
    public function sendEmail(array $data): SaltRoomReservationSendUpcomingReservationEmailResponse
    {
        $response = app(SaltRoomReservationSendUpcomingReservationEmailResponse::class, ['status' => 'Ok']);

        try {
            Mail::send(
                new MailReservation($data, $this)
            );
            $response->setSuccess();
        } catch (Exception $ex) {
            logger($ex);
            $response->setErrors([$ex->getMessage()]);
        }

        return $response;
    }

    /**
     * @throws UnknownProperties
     * @throws InvalidStatusException
     */
    private function isNotifyEmail($data, $record)
    {
        if (isset($data['notify']) && $data['notify']) {
            $record = $this->find($record->id, ['client', 'orderDetails', 'orderDetails.order', 'orderDetails.product']);
            $dataEmail = [
                'id' => $record->id,
                'email' => $data['notification_email']
            ];
            $this->sendEmail($dataEmail);
        }
    }
}
