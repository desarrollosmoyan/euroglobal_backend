<?php

namespace Domain\Gyms\Services;

use Domain\Gyms\Actions\DeleteGymFeeType;
use Domain\Gyms\Actions\DeleteGymSubscription;
use Domain\Gyms\Actions\DeleteGymSubscriptionMember;
use Domain\Gyms\Actions\DeleteGymSubscriptionMemberAccess;
use Domain\Gyms\Actions\DeleteGymSubscriptionMemberAccessRight;
use Domain\Gyms\Actions\DeleteGymSubscriptionNote;
use Domain\Gyms\Actions\DeleteGymSubscriptionPayment;
use Domain\Gyms\Actions\DeleteGymSubscriptionPaymentDetail;
use Domain\Gyms\Actions\DeleteGymSubscriptionVersion;
use Domain\Gyms\Actions\MakeVersionGymSubscription;
use Domain\Gyms\Actions\PayQuota;
use Domain\Gyms\Actions\UpsertGymFeeType;
use Domain\Gyms\Actions\UpsertGymSubscription;
use Domain\Gyms\Actions\UpsertGymSubscriptionMember;
use Domain\Gyms\Actions\UpsertGymSubscriptionMemberAccess;
use Domain\Gyms\Actions\UpsertGymSubscriptionMemberAccessRight;
use Domain\Gyms\Actions\UpsertGymSubscriptionNote;
use Domain\Gyms\Actions\UpsertGymSubscriptionPayment;
use Domain\Gyms\Actions\UpsertGymSubscriptionPaymentDetail;
use Domain\Gyms\Actions\UpsertGymSubscriptionVersion;
use Domain\Gyms\Contracts\Repositories\GymFeeTypesRepository;
use Domain\Gyms\Contracts\Repositories\GymSubscriptionMemberAccessRepository;
use Domain\Gyms\Contracts\Repositories\GymSubscriptionMemberAccessRightsRepository;
use Domain\Gyms\Contracts\Repositories\GymSubscriptionMembersRepository;
use Domain\Gyms\Contracts\Repositories\GymSubscriptionNotesRepository;
use Domain\Gyms\Contracts\Repositories\GymSubscriptionPaymentDetailsRepository;
use Domain\Gyms\Contracts\Repositories\GymSubscriptionPaymentsRepository;
use Domain\Gyms\Contracts\Repositories\GymSubscriptionsRepository;
use Domain\Gyms\Contracts\Repositories\GymSubscriptionVersionRepository;
use Domain\Gyms\DataTransferObjects\GymFeeTypeEntitiesCollection;
use Domain\Gyms\DataTransferObjects\GymFeeTypeEntity;
use Domain\Gyms\DataTransferObjects\GymFeeTypeSearchRequest;
use Domain\Gyms\DataTransferObjects\GymFeeTypeSearchResponse;
use Domain\Gyms\DataTransferObjects\GymSubscriptionEntitiesCollection;
use Domain\Gyms\DataTransferObjects\GymSubscriptionEntity;
use Domain\Gyms\DataTransferObjects\GymSubscriptionMemberAccessEntitiesCollection;
use Domain\Gyms\DataTransferObjects\GymSubscriptionMemberAccessEntity;
use Domain\Gyms\DataTransferObjects\GymSubscriptionMemberAccessRightEntitiesCollection;
use Domain\Gyms\DataTransferObjects\GymSubscriptionMemberAccessRightEntity;
use Domain\Gyms\DataTransferObjects\GymSubscriptionMemberAccessRightSearchRequest;
use Domain\Gyms\DataTransferObjects\GymSubscriptionMemberAccessRightSearchResponse;
use Domain\Gyms\DataTransferObjects\GymSubscriptionMemberAccessSearchRequest;
use Domain\Gyms\DataTransferObjects\GymSubscriptionMemberAccessSearchResponse;
use Domain\Gyms\DataTransferObjects\GymSubscriptionMemberEntitiesCollection;
use Domain\Gyms\DataTransferObjects\GymSubscriptionMemberEntity;
use Domain\Gyms\DataTransferObjects\GymSubscriptionMemberSearchRequest;
use Domain\Gyms\DataTransferObjects\GymSubscriptionMemberSearchResponse;
use Domain\Gyms\DataTransferObjects\GymSubscriptionNoteEntitiesCollection;
use Domain\Gyms\DataTransferObjects\GymSubscriptionNoteEntity;
use Domain\Gyms\DataTransferObjects\GymSubscriptionNoteSearchRequest;
use Domain\Gyms\DataTransferObjects\GymSubscriptionNoteSearchResponse;
use Domain\Gyms\DataTransferObjects\GymSubscriptionPaymentDetailEntitiesCollection;
use Domain\Gyms\DataTransferObjects\GymSubscriptionPaymentDetailEntity;
use Domain\Gyms\DataTransferObjects\GymSubscriptionPaymentDetailSearchRequest;
use Domain\Gyms\DataTransferObjects\GymSubscriptionPaymentDetailSearchResponse;
use Domain\Gyms\DataTransferObjects\GymSubscriptionPaymentEntitiesCollection;
use Domain\Gyms\DataTransferObjects\GymSubscriptionPaymentEntity;
use Domain\Gyms\DataTransferObjects\GymSubscriptionPaymentSearchRequest;
use Domain\Gyms\DataTransferObjects\GymSubscriptionPaymentSearchResponse;
use Domain\Gyms\DataTransferObjects\GymSubscriptionSearchRequest;
use Domain\Gyms\DataTransferObjects\GymSubscriptionSearchResponse;
use Domain\Gyms\DataTransferObjects\GymSubscriptionVersionEntitiesCollection;
use Domain\Gyms\DataTransferObjects\GymSubscriptionVersionEntity;
use Domain\Gyms\DataTransferObjects\GymSubscriptionVersionSearchRequest;
use Domain\Gyms\DataTransferObjects\GymSubscriptionVersionSearchResponse;
use Domain\Gyms\Models\GymFeeType;
use Domain\Gyms\Models\GymSubscription;
use Domain\Gyms\Models\GymSubscriptionMember;
use Domain\Gyms\Models\GymSubscriptionMemberAccess;
use Domain\Gyms\Models\GymSubscriptionMemberAccessRight;
use Domain\Gyms\Models\GymSubscriptionNote;
use Domain\Gyms\Models\GymSubscriptionPayment;
use Domain\Gyms\Models\GymSubscriptionPaymentDetail;
use Domain\Gyms\Models\GymSubscriptionVersion;
use Domain\Gyms\Transformers\GymFeeTypeTransformer;
use Domain\Gyms\Transformers\GymSubscriptionMemberAccessRightTransformer;
use Domain\Gyms\Transformers\GymSubscriptionMemberAccessTransformer;
use Domain\Gyms\Transformers\GymSubscriptionMemberTransformer;
use Domain\Gyms\Transformers\GymSubscriptionNoteTransformer;
use Domain\Gyms\Transformers\GymSubscriptionPaymentDetailTransformer;
use Domain\Gyms\Transformers\GymSubscriptionPaymentTransformer;
use Domain\Gyms\Transformers\GymSubscriptionTransformer;
use Domain\Gyms\Transformers\GymSubscriptionVersionTransformer;
use Exception;
use Illuminate\Support\Facades\DB;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;
use Support\Core\Serializers\FractalDataArraySerializer;
use Support\Exceptions\InvalidDataTypeException;

class GymsService implements \Domain\Gyms\Contracts\Services\GymsService
{
    /**
     * @var GymFeeTypesRepository
     */
    protected GymFeeTypesRepository $gymFeeTypesRepository;

    /**
     * @var GymSubscriptionsRepository
     */
    protected GymSubscriptionsRepository $gymSubscriptionsRepository;

    /**
     * @var GymSubscriptionMembersRepository
     */
    protected GymSubscriptionMembersRepository $gymSubscriptionMembersRepository;

    /**
     * @var GymSubscriptionMemberAccessRepository
     */
    protected GymSubscriptionMemberAccessRepository $gymSubscriptionMemberAccessRepository;

    /**
     * @var GymSubscriptionMemberAccessRightsRepository
     */
    protected GymSubscriptionMemberAccessRightsRepository $gymSubscriptionMemberAccessRightsRepository;

    /**
     * @var GymSubscriptionNotesRepository
     */
    protected GymSubscriptionNotesRepository $gymSubscriptionNotesRepository;

    /**
     * @var GymSubscriptionPaymentsRepository
     */
    protected GymSubscriptionPaymentsRepository $gymSubscriptionPaymentsRepository;

    /**
     * @var GymSubscriptionPaymentDetailsRepository
     */
    protected GymSubscriptionPaymentDetailsRepository $gymSubscriptionPaymentDetailsRepository;

    /**
     * @var GymSubscriptionVersionRepository
     */
    protected GymSubscriptionVersionRepository $gymSubscriptionVersionRepository;


    /**
     * @param GymFeeTypesRepository $gymFeeTypesRepository
     * @param GymSubscriptionsRepository $gymSubscriptionsRepository
     * @param GymSubscriptionMembersRepository $gymSubscriptionMembersRepository
     * @param GymSubscriptionMemberAccessRepository $gymSubscriptionMemberAccessRepository
     * @param GymSubscriptionMemberAccessRightsRepository $gymSubscriptionMemberAccessRightsRepository
     * @param GymSubscriptionPaymentsRepository $gymSubscriptionPaymentsRepository
     * @param GymSubscriptionPaymentDetailsRepository $gymSubscriptionPaymentDetailsRepository
     * @param GymSubscriptionVersionRepository $gymSubscriptionVersionRepository
     */
    public function __construct(
        GymFeeTypesRepository $gymFeeTypesRepository,
        GymSubscriptionsRepository $gymSubscriptionsRepository,
        GymSubscriptionMembersRepository $gymSubscriptionMembersRepository,
        GymSubscriptionMemberAccessRepository $gymSubscriptionMemberAccessRepository,
        GymSubscriptionMemberAccessRightsRepository $gymSubscriptionMemberAccessRightsRepository,
        GymSubscriptionNotesRepository $gymSubscriptionNotesRepository,
        GymSubscriptionPaymentsRepository $gymSubscriptionPaymentsRepository,
        GymSubscriptionPaymentDetailsRepository $gymSubscriptionPaymentDetailsRepository,
        GymSubscriptionVersionRepository $gymSubscriptionVersionRepository,
    ) {
        $this->gymFeeTypesRepository = $gymFeeTypesRepository;
        $this->gymSubscriptionsRepository = $gymSubscriptionsRepository;
        $this->gymSubscriptionMembersRepository = $gymSubscriptionMembersRepository;
        $this->gymSubscriptionMemberAccessRepository = $gymSubscriptionMemberAccessRepository;
        $this->gymSubscriptionMemberAccessRightsRepository = $gymSubscriptionMemberAccessRightsRepository;
        $this->gymSubscriptionNotesRepository = $gymSubscriptionNotesRepository;
        $this->gymSubscriptionPaymentsRepository = $gymSubscriptionPaymentsRepository;
        $this->gymSubscriptionPaymentDetailsRepository = $gymSubscriptionPaymentDetailsRepository;
        $this->gymSubscriptionVersionRepository = $gymSubscriptionVersionRepository;
    }

    /**
     * @param array $data
     * @return GymFeeTypeEntity
     * @throws UnknownProperties
     */
    public function createGymFeeType(array $data): GymFeeTypeEntity
    {
        $record = app(UpsertGymFeeType::class)($data);

        return $this->gymFeeTypeDTOFromModel($record);
    }

    /**
     * @param array $data
     * @return GymSubscriptionEntity
     * @throws UnknownProperties
     */
    public function createGymSubscription(array $data): GymSubscriptionEntity
    {
        $record = app(UpsertGymSubscription::class)($data);

        return $this->gymSubscriptionDTOFromModel($record);
    }

    /**
     * @param array $data
     * @return GymSubscriptionMemberEntity
     * @throws UnknownProperties
     */
    public function createGymSubscriptionMember(array $data): GymSubscriptionMemberEntity
    {
        $record = app(UpsertGymSubscriptionMember::class)($data);

        return $this->gymSubscriptionMemberDTOFromModel($record);
    }

    /**
     * @param array $data
     * @return GymSubscriptionMemberAccessEntity
     * @throws UnknownProperties
     */
    public function createGymSubscriptionMemberAccess(array $data): GymSubscriptionMemberAccessEntity
    {
        $record = app(UpsertGymSubscriptionMemberAccess::class)($data);

        return $this->gymSubscriptionMemberAccessDTOFromModel($record);
    }

    /**
     * @param array $data
     * @return GymSubscriptionMemberAccessRightEntity
     * @throws UnknownProperties
     */
    public function createGymSubscriptionMemberAccessRight(array $data): GymSubscriptionMemberAccessRightEntity
    {
        $record = app(UpsertGymSubscriptionMemberAccessRight::class)($data);

        return $this->gymSubscriptionMemberAccessRightDTOFromModel($record);
    }

    /**
     * @param array $data
     * @return GymSubscriptionNoteEntity
     * @throws UnknownProperties
     */
    public function createGymSubscriptionNote(array $data): GymSubscriptionNoteEntity
    {
        $record = app(UpsertGymSubscriptionNote::class)($data);

        return $this->gymSubscriptionNoteDTOFromModel($record);
    }

    /**
     * @param array $data
     * @param array $includes
     * @return GymSubscriptionPaymentEntity
     * @throws UnknownProperties
     */
    public function createGymSubscriptionPayment(array $data, array $includes = []): GymSubscriptionPaymentEntity
    {
        $record = app(UpsertGymSubscriptionPayment::class)($data);

        return $this->gymSubscriptionPaymentDTOFromModel($record);
    }

    /**
     * @param array $data
     * @param array $includes
     * @return GymSubscriptionPaymentDetailEntity
     * @throws UnknownProperties
     */
    public function createGymSubscriptionPaymentDetail(array $data, array $includes = []): GymSubscriptionPaymentDetailEntity
    {
        $record = app(UpsertGymSubscriptionPaymentDetail::class)($data);

        return $this->gymSubscriptionPaymentDetailDTOFromModel($record, $includes);
    }

    /**
     * @param array $data
     * @return GymSubscriptionVersionEntity
     * @throws UnknownProperties
     */
    public function createGymSubscriptionVersion(array $data): GymSubscriptionVersionEntity
    {
        $record = app(UpsertGymSubscriptionVersion::class)($data);

        return $this->gymSubscriptionVersionDTOFromModel($record);
    }

    /**
     * @param array $data
     * @return GymFeeTypeEntity
     * @throws UnknownProperties
     */
    public function deleteGymFeeType(array $data): GymFeeTypeEntity
    {
        $record = app(DeleteGymFeeType::class)($data);

        return $this->gymFeeTypeDTOFromModel($record);
    }

    /**
     * @param array $data
     * @return GymSubscriptionEntity
     * @throws UnknownProperties
     */
    public function deleteGymSubscription(array $data): GymSubscriptionEntity
    {
        $record = app(DeleteGymSubscription::class)($data);

        return $this->gymSubscriptionDTOFromModel($record);
    }

    /**
     * @param array $data
     * @return GymSubscriptionMemberEntity
     * @throws UnknownProperties
     */
    public function deleteGymSubscriptionMember(array $data): GymSubscriptionMemberEntity
    {
        $record = app(DeleteGymSubscriptionMember::class)($data);

        return $this->gymSubscriptionMemberDTOFromModel($record);
    }

    /**
     * @param array $data
     * @return GymSubscriptionMemberAccessEntity
     * @throws UnknownProperties
     */
    public function deleteGymSubscriptionMemberAccess(array $data): GymSubscriptionMemberAccessEntity
    {
        $record = app(DeleteGymSubscriptionMemberAccess::class)($data);

        return $this->gymSubscriptionMemberAccessDTOFromModel($record);
    }

    /**
     * @param array $data
     * @return GymSubscriptionMemberAccessRightEntity
     * @throws UnknownProperties
     */
    public function deleteGymSubscriptionMemberAccessRight(array $data): GymSubscriptionMemberAccessRightEntity
    {
        $record = app(DeleteGymSubscriptionMemberAccessRight::class)($data);

        return $this->gymSubscriptionMemberAccessRightDTOFromModel($record);
    }

     /**
     * @param array $data
     * @return GymSubscriptionNoteEntity
     * @throws UnknownProperties
     */
    public function deleteGymSubscriptionNote(array $data): GymSubscriptionNoteEntity
    {
        $record = app(DeleteGymSubscriptionNote::class)($data);

        return $this->gymSubscriptionNoteDTOFromModel($record);
    }

    /**
     * @param array $data
     * @return GymSubscriptionPaymentEntity
     * @throws UnknownProperties
     */
    public function deleteGymSubscriptionPayment(array $data): GymSubscriptionPaymentEntity
    {
        $record = app(DeleteGymSubscriptionPayment::class)($data);

        return $this->gymSubscriptionPaymentDTOFromModel($record);
    }

    /**
     * @param array $data
     * @return GymSubscriptionPaymentEntity
     * @throws UnknownProperties
     */
    public function deleteGymSubscriptionPaymentDetail(array $data): GymSubscriptionPaymentDetailEntity
    {
        $record = app(DeleteGymSubscriptionPaymentDetail::class)($data);

        return $this->gymSubscriptionPaymentDetailDTOFromModel($record);
    }

    /**
     * @param array $data
     * @return GymSubscriptionVersionEntity
     * @throws UnknownProperties
     */
    public function deleteGymSubscriptionVersion(array $data): GymSubscriptionVersionEntity
    {
        $record = app(DeleteGymSubscriptionVersion::class)($data);

        return $this->gymSubscriptionVersionDTOFromModel($record);
    }

    /**
     * @throws UnknownProperties
     */
    public function findGymFeeType(int $id, array $includes = []): ?GymFeeTypeEntity
    {
        if (!$record = $this->gymFeeTypesRepository->find($id)) {
            return null;
        }

        return $this->gymFeeTypeDTOFromModel($record, $includes);
    }

    /**
     * @throws UnknownProperties
     */
    public function findGymSubscription(int $id, array $includes = []): ?GymSubscriptionEntity
    {
        if (!$record = $this->gymSubscriptionsRepository->find($id)) {
            return null;
        }

        return $this->gymSubscriptionDTOFromModel($record, $includes);
    }

    /**
     * @throws UnknownProperties
     */
    public function findGymSubscriptionMember(int $id, array $includes = []): ?GymSubscriptionMemberEntity
    {
        if (!$record = $this->gymSubscriptionMembersRepository->find($id)) {
            return null;
        }

        return $this->gymSubscriptionMemberDTOFromModel($record, $includes);
    }

    /**
     * @throws UnknownProperties
     */
    public function findGymSubscriptionMemberAccess(int $id, array $includes = []): ?GymSubscriptionMemberAccessEntity
    {
        if (!$record = $this->gymSubscriptionMemberAccessRepository->find($id)) {
            return null;
        }

        return $this->gymSubscriptionMemberAccessDTOFromModel($record, $includes);
    }

    /**
     * @throws UnknownProperties
     */
    public function findGymSubscriptionMemberAccessRight(
        int $id,
        array $includes = []
    ): ?GymSubscriptionMemberAccessRightEntity {
        if (!$record = $this->gymSubscriptionMemberAccessRightsRepository->find($id)) {
            return null;
        }

        return $this->gymSubscriptionMemberAccessRightDTOFromModel($record, $includes);
    }

    /**
     * @param integer $id
     * @param array $includes
     * @return GymSubscriptionNoteEntity|null
     * @throws UnknownProperties
     */
    public function findGymSubscriptionNote(int $id, array $includes = []): ?GymSubscriptionNoteEntity
    {
        if (!$record = $this->gymSubscriptionNotesRepository->find($id)) {
            return null;
        }

        return $this->gymSubscriptionNoteDTOFromModel($record, $includes);
    }

    /**
     * @throws UnknownProperties
     */
    public function findGymSubscriptionPayment(int $id, array $includes = []): ?GymSubscriptionPaymentEntity
    {
        if (!$record = $this->gymSubscriptionPaymentsRepository->find($id)) {
            return null;
        }

        return $this->gymSubscriptionPaymentDTOFromModel($record, $includes);
    }

    /**
     * @throws UnknownProperties
     */
    public function findGymSubscriptionPaymentDetail(int $id, array $includes = []): ?GymSubscriptionPaymentDetailEntity
    {
        if (!$record = $this->gymSubscriptionPaymentDetailsRepository->find($id)) {
            return null;
        }

        return $this->gymSubscriptionPaymentDetailDTOFromModel($record, $includes);
    }

    /**
     * Paga la cuota de la subscripción y extiende la fecha de vencimiento
     *
     * @param integer $id
     * @return GymSubscriptionEntity
     * @throws UnknownProperties
     */
    public function payQuota(int $id): GymSubscriptionEntity
    {
        return app(PayQuota::class)($id);
    }

     /**
     * @throws UnknownProperties
     */
    public function findGymSubscriptionVersion(int $id, array $includes = []): ?GymSubscriptionVersionEntity
    {
        if (!$record = $this->gymSubscriptionVersionRepository->find($id)) {
            return null;
        }

        return $this->gymSubscriptionVersionDTOFromModel($record, $includes);
    }

    /**
     * @param array $data
     * @return GymSubscriptionEntity
     */
    public function makeVersion(array $data): GymSubscriptionEntity
    {
        if (!$subscription = $this->findGymSubscription($data['id'])) {
            throw new Exception('Gym subscription not found');
        }

        $subscriptionArray = $subscription->toArray();
        $subscriptionVersion = array_replace($subscriptionArray, ['gym_subscription_id' => $subscriptionArray['id']]);
        unset($subscriptionVersion['id']);

        $result = DB::transaction(function () use ($subscriptionVersion, $data): ?GymSubscriptionEntity {
            app(UpsertGymSubscriptionVersion::class)($subscriptionVersion);
            $record = app(MakeVersionGymSubscription::class)($data);

            return $this->gymSubscriptionDTOFromModel($record);
        });

        if (!$result) {
            throw new Exception('Gym subscription version could not be created');
        }

        return $result;
    }

    /**
     * @param GymFeeTypeSearchRequest $request
     * @return GymFeeTypeSearchResponse
     * @throws UnknownProperties
     * @throws InvalidDataTypeException
     */
    public function searchGymFeeTypes(GymFeeTypeSearchRequest $request): GymFeeTypeSearchResponse
    {
        $query = $this->gymFeeTypesRepository->searchQueryBuilder(
            $request->filters,
            $request->sortField,
            $request->sortType
        );
        $records = $query->paginate($request->paginateSize);
        $collection = new Collection($records->items(), app(GymFeeTypeTransformer::class), 'data');
        $collection->setPaginator(new IlluminatePaginatorAdapter($records));
        $manager = app(Manager::class);
        $manager->parseIncludes($request->includes);
        $manager->setSerializer(new FractalDataArraySerializer());
        $resourceCollection = $manager->createData($collection);

        $result = $resourceCollection->toArray();

        return (new GymFeeTypeSearchResponse('Ok'))->setData(
            GymFeeTypeEntitiesCollection::createFromArray($result['data'])
        )->setMeta($result['meta']);
    }

    /**
     * @param GymSubscriptionMemberAccessSearchRequest $request
     * @return GymSubscriptionMemberAccessSearchResponse
     * @throws UnknownProperties
     * @throws InvalidDataTypeException
     */
    public function searchGymSubscriptionMemberAccess(
        GymSubscriptionMemberAccessSearchRequest $request
    ): GymSubscriptionMemberAccessSearchResponse {
        $query = $this->gymSubscriptionMemberAccessRepository->searchQueryBuilder(
            $request->filters,
            $request->sortField,
            $request->sortType
        );
        $records = $query->paginate($request->paginateSize);
        $collection = new Collection($records->items(), app(GymSubscriptionMemberAccessTransformer::class), 'data');
        $collection->setPaginator(new IlluminatePaginatorAdapter($records));
        $manager = app(Manager::class);
        $manager->parseIncludes($request->includes);
        $manager->setSerializer(new FractalDataArraySerializer());
        $resourceCollection = $manager->createData($collection);

        $result = $resourceCollection->toArray();

        return (new GymSubscriptionMemberAccessSearchResponse('Ok'))->setData(
            GymSubscriptionMemberAccessEntitiesCollection::createFromArray($result['data'])
        )->setMeta($result['meta']);
    }

    /**
     * @param GymSubscriptionMemberAccessRightSearchRequest $request
     * @return GymSubscriptionMemberAccessRightSearchResponse
     * @throws UnknownProperties
     * @throws InvalidDataTypeException
     */
    public function searchGymSubscriptionMemberAccessRights(
        GymSubscriptionMemberAccessRightSearchRequest $request
    ): GymSubscriptionMemberAccessRightSearchResponse {
        $query = $this->gymSubscriptionMemberAccessRightsRepository->searchQueryBuilder(
            $request->filters,
            $request->sortField,
            $request->sortType
        );
        $records = $query->paginate($request->paginateSize);
        $collection = new Collection($records->items(), app(GymSubscriptionMemberAccessRightTransformer::class), 'data');
        $collection->setPaginator(new IlluminatePaginatorAdapter($records));
        $manager = app(Manager::class);
        $manager->parseIncludes($request->includes);
        $manager->setSerializer(new FractalDataArraySerializer());
        $resourceCollection = $manager->createData($collection);

        $result = $resourceCollection->toArray();

        return (new GymSubscriptionMemberAccessRightSearchResponse('Ok'))->setData(
            GymSubscriptionMemberAccessRightEntitiesCollection::createFromArray($result['data'])
        )->setMeta($result['meta']);
    }

    /**
     * @param GymSubscriptionMemberSearchRequest $request
     * @return GymSubscriptionMemberSearchResponse
     * @throws UnknownProperties
     * @throws InvalidDataTypeException
     */
    public function searchGymSubscriptionMembers(
        GymSubscriptionMemberSearchRequest $request
    ): GymSubscriptionMemberSearchResponse {
        $query = $this->gymSubscriptionMembersRepository->searchQueryBuilder(
            $request->filters,
            $request->sortField,
            $request->sortType
        );
        $records = $query->paginate($request->paginateSize);
        $collection = new Collection($records->items(), app(GymSubscriptionMemberTransformer::class), 'data');
        $collection->setPaginator(new IlluminatePaginatorAdapter($records));
        $manager = app(Manager::class);
        $manager->parseIncludes($request->includes);
        $manager->setSerializer(new FractalDataArraySerializer());
        $resourceCollection = $manager->createData($collection);

        $result = $resourceCollection->toArray();

        return (new GymSubscriptionMemberSearchResponse('Ok'))->setData(
            GymSubscriptionMemberEntitiesCollection::createFromArray($result['data'])
        )->setMeta($result['meta']);
    }

    /**
     * @param GymSubscriptionNoteSearchRequest $request
     * @return GymSubscriptionNoteSearchResponse
     * @throws InvalidDataTypeException
     * @throws UnknownProperties
     */
    public function searchGymSubscriptionNotes(GymSubscriptionNoteSearchRequest $request): GymSubscriptionNoteSearchResponse
    {
        $query = $this->gymSubscriptionNotesRepository->searchQueryBuilder(
            $request->filters,
            $request->sortField,
            $request->sortType
        );
        $records = $query->paginate($request->paginateSize);
        $collection = new Collection($records->items(), app(GymSubscriptionNoteTransformer::class), 'data');

        $collection->setPaginator(new IlluminatePaginatorAdapter($records));
        $manager = app(Manager::class);
        $manager->parseIncludes($request->includes);
        $manager->setSerializer(new FractalDataArraySerializer());
        $resourceCollection = $manager->createData($collection);

        $result = $resourceCollection->toArray();

        return (new GymSubscriptionNoteSearchResponse('Ok'))->setData(
            GymSubscriptionNoteEntitiesCollection::createFromArray($result['data'])
        )->setMeta($result['meta']);
    }

    /**
     * @param GymSubscriptionPaymentDetailSearchRequest $request
     * @return GymSubscriptionPaymentDetailSearchResponse
     * @throws InvalidDataTypeException
     */
    public function searchGymSubscriptionPaymentDetails(
        GymSubscriptionPaymentDetailSearchRequest $request
    ): GymSubscriptionPaymentDetailSearchResponse {
        $query = $this->gymSubscriptionPaymentDetailsRepository->searchQueryBuilder(
            $request->filters,
            $request->sortField,
            $request->sortType
        );
        $records = $query->paginate($request->paginateSize);
        $collection = new Collection($records->items(), app(GymSubscriptionPaymentDetailTransformer::class), 'data');
        $collection->setPaginator(new IlluminatePaginatorAdapter($records));
        $manager = app(Manager::class);
        $manager->parseIncludes($request->includes);
        $manager->setSerializer(new FractalDataArraySerializer());
        $resourceCollection = $manager->createData($collection);

        $result = $resourceCollection->toArray();

        return (new GymSubscriptionPaymentDetailSearchResponse('Ok'))->setData(
            GymSubscriptionPaymentDetailEntitiesCollection::createFromArray($result['data'])
        )->setMeta($result['meta']);
    }

    /**
     * @param GymSubscriptionPaymentSearchRequest $request
     * @return GymSubscriptionPaymentSearchResponse
     * @throws UnknownProperties
     * @throws InvalidDataTypeException
     */
    public function searchGymSubscriptionPayments(
        GymSubscriptionPaymentSearchRequest $request
    ): GymSubscriptionPaymentSearchResponse {
        $query = $this->gymSubscriptionPaymentsRepository->searchQueryBuilder(
            $request->filters,
            $request->sortField,
            $request->sortType
        );
        $records = $query->paginate($request->paginateSize);
        $collection = new Collection($records->items(), app(GymSubscriptionPaymentTransformer::class), 'data');
        $collection->setPaginator(new IlluminatePaginatorAdapter($records));
        $manager = app(Manager::class);
        $manager->parseIncludes($request->includes);
        $manager->setSerializer(new FractalDataArraySerializer());
        $resourceCollection = $manager->createData($collection);

        $result = $resourceCollection->toArray();

        return (new GymSubscriptionPaymentSearchResponse('Ok'))->setData(
            GymSubscriptionPaymentEntitiesCollection::createFromArray($result['data'])
        )->setMeta($result['meta']);
    }

    /**
     * @param GymSubscriptionVersionSearchRequest $request
     * @return GymSubscriptionVersionSearchResponse
     * @throws UnknownProperties
     * @throws InvalidDataTypeException
     */
    public function searchGymSubscriptionVersion(GymSubscriptionVersionSearchRequest $request): GymSubscriptionVersionSearchResponse
    {
        $query = $this->gymSubscriptionVersionRepository->searchQueryBuilder(
            $request->filters,
            $request->sortField,
            $request->sortType
        );
        $records = $query->paginate($request->paginateSize);
        $collection = new Collection($records->items(), app(GymSubscriptionVersionTransformer::class), 'data');
        $collection->setPaginator(new IlluminatePaginatorAdapter($records));
        $manager = app(Manager::class);
        $manager->parseIncludes($request->includes);
        $manager->setSerializer(new FractalDataArraySerializer());
        $resourceCollection = $manager->createData($collection);

        $result = $resourceCollection->toArray();

        return (new GymSubscriptionVersionSearchResponse('Ok'))->setData(
            GymSubscriptionVersionEntitiesCollection::createFromArray($result['data'])
        )->setMeta($result['meta']);
    }

    /**
     * @param GymSubscriptionSearchRequest $request
     * @return GymSubscriptionSearchResponse
     * @throws UnknownProperties
     * @throws InvalidDataTypeException
     */
    public function searchGymSubscriptions(GymSubscriptionSearchRequest $request): GymSubscriptionSearchResponse
    {
        $query = $this->gymSubscriptionsRepository->searchQueryBuilder(
            $request->filters,
            $request->sortField,
            $request->sortType
        );
        $records = $query->paginate($request->paginateSize);
        $collection = new Collection($records->items(), app(GymSubscriptionTransformer::class), 'data');
        $collection->setPaginator(new IlluminatePaginatorAdapter($records));
        $manager = app(Manager::class);
        $manager->parseIncludes($request->includes);
        $manager->setSerializer(new FractalDataArraySerializer());
        $resourceCollection = $manager->createData($collection);

        $result = $resourceCollection->toArray();

        return (new GymSubscriptionSearchResponse('Ok'))->setData(
            GymSubscriptionEntitiesCollection::createFromArray($result['data'])
        )->setMeta($result['meta']);
    }

    /**
     * @param array $data
     * @return GymFeeTypeEntity
     * @throws UnknownProperties
     */
    public function updateGymFeeType(array $data): GymFeeTypeEntity
    {
        $record = app(UpsertGymFeeType::class)($data);

        return $this->gymFeeTypeDTOFromModel($record);
    }

    /**
     * @param array $data
     * @return GymSubscriptionEntity
     * @throws UnknownProperties
     */
    public function updateGymSubscription(array $data): GymSubscriptionEntity
    {
        $record = app(UpsertGymSubscription::class)($data);

        return $this->gymSubscriptionDTOFromModel($record);
    }

    /**
     * @param array $data
     * @return GymSubscriptionMemberEntity
     * @throws UnknownProperties
     */
    public function updateGymSubscriptionMember(array $data): GymSubscriptionMemberEntity
    {
        $record = app(UpsertGymSubscriptionMember::class)($data);

        return $this->gymSubscriptionMemberDTOFromModel($record);
    }

    /**
     * @param array $data
     * @return GymSubscriptionMemberAccessEntity
     * @throws UnknownProperties
     */
    public function updateGymSubscriptionMemberAccess(array $data): GymSubscriptionMemberAccessEntity
    {
        $record = app(UpsertGymSubscriptionMemberAccess::class)($data);

        return $this->gymSubscriptionMemberAccessDTOFromModel($record);
    }

    /**
     * @param array $data
     * @return GymSubscriptionMemberAccessRightEntity
     * @throws UnknownProperties
     */
    public function updateGymSubscriptionMemberAccessRight(array $data): GymSubscriptionMemberAccessRightEntity
    {
        $record = app(UpsertGymSubscriptionMemberAccessRight::class)($data);

        return $this->gymSubscriptionMemberAccessRightDTOFromModel($record);
    }

    /**
     * @param array $data
     * @return GymSubscriptionNoteEntity
     * @throws UnknownProperties
     */
    public function updateGymSubscriptionNote(array $data): GymSubscriptionNoteEntity
    {
        $record = app(UpsertGymSubscriptionNote::class)($data);

        return $this->gymSubscriptionNoteDTOFromModel($record);
    }

    /**
     * @param array $data
     * @param array $includes
     * @return GymSubscriptionPaymentEntity
     * @throws UnknownProperties
     */
    public function updateGymSubscriptionPayment(array $data, array $includes = []): GymSubscriptionPaymentEntity
    {
        $record = app(UpsertGymSubscriptionPayment::class)($data);

        return $this->gymSubscriptionPaymentDTOFromModel($record, $includes);
    }

    /**
     * @param array $data
     * @return GymSubscriptionPaymentDetailEntity
     * @throws UnknownProperties
     */
    public function updateGymSubscriptionPaymentDetail(array $data, array $includes = []): GymSubscriptionPaymentDetailEntity
    {
        $record = app(UpsertGymSubscriptionPaymentDetail::class)($data);

        return $this->gymSubscriptionPaymentDetailDTOFromModel($record, $includes);
    }

    /**
     * @param array $data
     * @return GymSubscriptionVersionEntity
     * @throws UnknownProperties
     */
    public function updateGymSubscriptionVersion(array $data): GymSubscriptionVersionEntity
    {
        $record = app(UpsertGymSubscriptionVersion::class)($data);

        return $this->gymSubscriptionVersionDTOFromModel($record);
    }

    /**
     * @param GymFeeType $entity
     * @param array $includes
     * @return GymFeeTypeEntity
     * @throws UnknownProperties
     */
    private function gymFeeTypeDTOFromModel(GymFeeType $entity, array $includes = []): GymFeeTypeEntity
    {
        $manager = app(Manager::class);
        $manager->parseIncludes($includes);
        $manager->setSerializer(new FractalDataArraySerializer());
        $item = new Item($entity, app(GymFeeTypeTransformer::class));
        $data = $manager->createData($item)->toArray();

        return new GymFeeTypeEntity($data);
    }

    /**
     * @param GymSubscription $entity
     * @param array $includes
     * @return GymSubscriptionEntity
     * @throws UnknownProperties
     */
    private function gymSubscriptionDTOFromModel(GymSubscription $entity, array $includes = []): GymSubscriptionEntity
    {
        $manager = app(Manager::class);
        $manager->parseIncludes($includes);
        $manager->setSerializer(new FractalDataArraySerializer());
        $item = new Item($entity, app(GymSubscriptionTransformer::class));
        $data = $manager->createData($item)->toArray();

        return new GymSubscriptionEntity($data);
    }

    /**
     * @param GymSubscriptionMemberAccess $entity
     * @param array $includes
     * @return GymSubscriptionMemberAccessEntity
     * @throws UnknownProperties
     */
    private function gymSubscriptionMemberAccessDTOFromModel(
        GymSubscriptionMemberAccess $entity,
        array $includes = []
    ): GymSubscriptionMemberAccessEntity {
        $manager = app(Manager::class);
        $manager->parseIncludes($includes);
        $manager->setSerializer(new FractalDataArraySerializer());
        $item = new Item($entity, app(GymSubscriptionMemberAccessTransformer::class));
        $data = $manager->createData($item)->toArray();

        return new GymSubscriptionMemberAccessEntity($data);
    }

    /**
     * @param GymSubscriptionMemberAccessRight $entity
     * @param array $includes
     * @return GymSubscriptionMemberAccessRightEntity
     * @throws UnknownProperties
     */
    private function gymSubscriptionMemberAccessRightDTOFromModel(
        GymSubscriptionMemberAccessRight $entity,
        array $includes = []
    ): GymSubscriptionMemberAccessRightEntity {
        $manager = app(Manager::class);
        $manager->parseIncludes($includes);
        $manager->setSerializer(new FractalDataArraySerializer());
        $item = new Item($entity, app(GymSubscriptionMemberAccessRightTransformer::class));
        $data = $manager->createData($item)->toArray();

        return new GymSubscriptionMemberAccessRightEntity($data);
    }

    /**
     * @param GymSubscriptionMember $entity
     * @param array $includes
     * @return GymSubscriptionMemberEntity
     * @throws UnknownProperties
     */
    private function gymSubscriptionMemberDTOFromModel(
        GymSubscriptionMember $entity,
        array $includes = []
    ): GymSubscriptionMemberEntity {
        $manager = app(Manager::class);
        $manager->parseIncludes($includes);
        $manager->setSerializer(new FractalDataArraySerializer());
        $item = new Item($entity, app(GymSubscriptionMemberTransformer::class));
        $data = $manager->createData($item)->toArray();

        return new GymSubscriptionMemberEntity($data);
    }

    /**
     * Undocumented function
     *
     * @param GymSubscriptionNote $entity
     * @param array $includes
     * @return GymSubscriptionNoteEntity
     * @throws UnknownProperties
     */
    protected function gymSubscriptionNoteDTOFromModel(GymSubscriptionNote $entity, array $includes = []): GymSubscriptionNoteEntity
    {
        $manager = app(Manager::class);
        $manager->parseIncludes($includes);
        $manager->setSerializer(new FractalDataArraySerializer());
        $item = new Item($entity, app(GymSubscriptionNoteTransformer::class));
        $data = $manager->createData($item)->toArray();

        return new GymSubscriptionNoteEntity($data);
    }

    /**
     * @param GymSubscriptionPayment $entity
     * @param array $includes
     * @return GymSubscriptionPaymentEntity
     * @throws UnknownProperties
     */
    private function gymSubscriptionPaymentDTOFromModel(
        GymSubscriptionPayment $entity,
        array $includes = []
    ): GymSubscriptionPaymentEntity {
        $manager = app(Manager::class);
        $manager->parseIncludes($includes);
        $manager->setSerializer(new FractalDataArraySerializer());
        $item = new Item($entity, app(GymSubscriptionPaymentTransformer::class));
        $data = $manager->createData($item)->toArray();

        return new GymSubscriptionPaymentEntity($data);
    }

    /**
     * @param GymSubscriptionPaymentDetail $entity
     * @param array $includes
     * @return GymSubscriptionPaymentDetailEntity
     * @throws UnknownProperties
     */
    private function gymSubscriptionPaymentDetailDTOFromModel(
        GymSubscriptionPaymentDetail $entity,
        array $includes = []
    ): GymSubscriptionPaymentDetailEntity {
        $manager = app(Manager::class);
        $manager->parseIncludes($includes);
        $manager->setSerializer(new FractalDataArraySerializer());
        $item = new Item($entity, app(GymSubscriptionPaymentDetailTransformer::class));
        $data = $manager->createData($item)->toArray();

        return new GymSubscriptionPaymentDetailEntity($data);
    }

    /**
     * @param GymSubscriptionVersion $entity
     * @param array $includes
     * @return GymSubscriptionVersionEntity
     * @throws UnknownProperties
     */
    private function gymSubscriptionVersionDTOFromModel(GymSubscriptionVersion $entity, array $includes = []): GymSubscriptionVersionEntity
    {
        $manager = app(Manager::class);
        $manager->parseIncludes($includes);
        $manager->setSerializer(new FractalDataArraySerializer());
        $item = new Item($entity, app(GymSubscriptionVersionTransformer::class));
        $data = $manager->createData($item)->toArray();

        return new GymSubscriptionVersionEntity($data);
    }
}
