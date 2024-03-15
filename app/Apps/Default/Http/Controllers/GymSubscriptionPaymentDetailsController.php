<?php

namespace Apps\Default\Http\Controllers;

use Domain\Gyms\Contracts\Services\GymsService;
use Domain\Gyms\DataTransferObjects\GymSubscriptionPaymentDetailSearchRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class GymSubscriptionPaymentDetailsController extends Controller
{
    /**
     * @var GymsService
     */
    protected GymsService $gymsService;

    /**
     * @param GymsService $gymsService
     */
    public function __construct(GymsService $gymsService)
    {
        $this->gymsService = $gymsService;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        return $this->executeAction(
            fn() => $this->apiOkResponse(
                $this->gymsService->createGymSubscriptionPaymentDetail(
                    $request->all(),
                    explode(',', $request->get('includes'))
                )
            )
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function delete(Request $request): JsonResponse
    {
        return $this->executeAction(
            fn() => $this->apiOkResponse(
                $this->gymsService->deleteGymSubscriptionPaymentDetail(
                    $request->all(),
                    explode(',', $request->get('includes'))
                )
            )
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws UnknownProperties
     */
    public function search(Request $request): JsonResponse
    {
        $result = $this->gymsService->searchGymSubscriptionPaymentDetails(
            new GymSubscriptionPaymentDetailSearchRequest([
                'filters' => $request->except(['includes', 'paginate_size']),
                'includes' => explode(',', $request->get('includes', '')),
                'paginateSize' => $request->get('paginate_size', 10)
            ])
        );

        return $this->apiOkResponse(['records' => $result->getData(), 'meta' => $result->getMeta()]);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function show(Request $request, int $id): JsonResponse
    {
        if ($result = $this->gymsService->findGymSubscriptionPaymentDetail(
            $id,
            explode(',', $request->get('includes'))
        )) {
            return $this->apiOkResponse($result->toArray());
        }

        return $this->apiRecordNotFoundResponse();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request): JsonResponse
    {
        return $this->executeAction(
            fn() => $this->apiOkResponse(
                $this->gymsService->updateGymSubscriptionPaymentDetail(
                    $request->all(),
                    explode(',', $request->get('includes'))
                )
            )
        );
    }
}
