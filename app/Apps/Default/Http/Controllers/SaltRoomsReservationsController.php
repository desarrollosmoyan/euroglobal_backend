<?php

namespace Apps\Default\Http\Controllers;

use Domain\SaltRoomReservations\Contracts\Services\SaltRoomReservationsService;
use Domain\SaltRoomReservations\DataTransferObjects\SaltRoomReservationSearchRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class SaltRoomsReservationsController extends Controller
{
    /**
     * @var SaltRoomReservationsService
     */
    private SaltRoomReservationsService $saltRoomReservationsService;

    /**
     * @param SaltRoomReservationsService $saltRoomReservationsService
     */
    public function __construct(SaltRoomReservationsService $saltRoomReservationsService)
    {
        $this->saltRoomReservationsService = $saltRoomReservationsService;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        return $this->executeAction(
            fn () => $this->apiOkResponse($this->saltRoomReservationsService->create($request->all()))
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function markAsUsed(Request $request): JsonResponse
    {
        return $this->executeAction(
            fn () => $this->apiOkResponse($this->saltRoomReservationsService->markAsUsed($request->all()))
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request): JsonResponse
    {
        return $this->executeAction(
            fn () => $this->apiOkResponse($this->saltRoomReservationsService->update($request->all()))
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function delete(Request $request): JsonResponse
    {
        return $this->executeAction(
            fn () => $this->apiOkResponse($this->saltRoomReservationsService->delete($request->all()))
        );
    }

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function show(Request $request, int $id): JsonResponse
    {
        if ($result = $this->saltRoomReservationsService->find($id, explode(',', $request->get('includes', '')))) {
            return $this->apiOkResponse($result->toArray());
        }

        return $this->apiRecordNotFoundResponse();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws UnknownProperties
     */
    public function search(Request $request): JsonResponse
    {
        $result = $this->saltRoomReservationsService->search(
            new SaltRoomReservationSearchRequest([
                'filters' => $request->except(['includes', 'paginate_size']),
                'includes' => explode(',', $request->get('includes', '')),
                'paginateSize' => $request->get('paginate_size', 10)
            ])
        );

        return $this->apiOkResponse(['records' => $result->getData(), 'meta' => $result->getMeta()]);
    }

    /**
     * @param string $date
     * @return JsonResponse
     */
    public function schedulesPdf(string $date): JsonResponse
    {
        $pdf = $this->saltRoomReservationsService->schedulesPdf($date);

        if ($pdf->isSuccess()) {
            return $this->apiOkResponse($pdf->getData());
        }

        return $this->apiErrorResponse([], ['PDF can\'t be generated']);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     * @throws UnknownProperties
     */
    public function summary(Request $request): JsonResponse
    {
        $result = $this->saltRoomReservationsService->search(
            new SaltRoomReservationSearchRequest([
                'filters' => $request->except(['includes', 'paginate_size']),
                'includes' => explode(',', $request->get('includes', '')),
                'paginateSize' => config('system.infinite_pagination')
            ])
        );

        $responseData = $result->getData();

        if ($request->has('group_results')) {
            $responseData = $responseData->groupBy(explode(',', $request->get('group_results')));
        }

        return $this->apiOkResponse($responseData);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function sendEmail(Request $request): JsonResponse
    {
        return $this->executeAction(
            fn () => $this->apiOkResponse($this->saltRoomReservationsService->sendEmail($request->all()))
        );
    }

}
