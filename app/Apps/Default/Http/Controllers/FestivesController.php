<?php

namespace Apps\Default\Http\Controllers;

use Illuminate\Http\Request;
use Support\Core\Enums\SQLSort;
use Illuminate\Http\JsonResponse;
use Domain\Festives\Contracts\Services\FestivesService;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;
use Domain\Festives\DataTransferObjects\FestiveSearchRequest;

class FestivesController extends Controller
{
    /**
     * @var FestivesService
     */
    protected FestivesService $festivesService;

    /**
     * @param FestivesService $festivesService
     */
    public function __construct(FestivesService $festivesService)
    {
        $this->festivesService = $festivesService;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws UnknownProperties
     */
    public function search(Request $request): JsonResponse
    {
        $result = $this->festivesService->search(
            new FestiveSearchRequest([
                'filters' => $request->except(['includes', 'paginate_size']),
                'includes' => explode(',', $request->get('includes', '')),
                'paginateSize' => $request->get('paginate_size', 10),
                'sortField' => $request->get('sort_field', 'id'),
                'sortType' => SQLSort::from($request->get('sort_type', 'DESC')),
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
        if ($result = $this->festivesService->find($id, explode(',', $request->get('includes')))) {
            return $this->apiOkResponse($result->toArray());
        }

        return $this->apiRecordNotFoundResponse();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        return $this->executeAction(
            fn() => $this->apiOkResponse($this->festivesService->create($request->all()))
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function delete(Request $request): JsonResponse
    {
        return $this->executeAction(
            fn() => $this->apiOkResponse($this->festivesService->delete($request->all()))
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request): JsonResponse
    {
        return $this->executeAction(
            fn() => $this->apiOkResponse($this->festivesService->update($request->all()))
        );
    }
}
