<?php

namespace Apps\Default\Http\Controllers;

use Domain\Employees\Contracts\Services\EmployeesService;
use Domain\Employees\DataTransferObjects\EmployeeWorkingHoursSearchRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;
use Support\Core\Enums\SQLSort;

class EmployeeWorkingHoursController extends Controller
{
    /**
     * @var EmployeesService
     */
    protected EmployeesService $employeesService;

    /**
     * @param EmployeesService $employeesService
     */
    public function __construct(EmployeesService $employeesService)
    {
        $this->employeesService = $employeesService;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws UnknownProperties
     */
    public function search(Request $request): JsonResponse
    {
        $result = $this->employeesService->searchEmployeeWorkingHours(
            new EmployeeWorkingHoursSearchRequest([
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
        if ($result = $this->employeesService->findEmployeeWorkingHours($id, explode(',', $request->get('includes')))) {
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
            fn () => $this->apiOkResponse($this->employeesService->createEmployeeWorkingHours($request->all()))
        );
    }

    public function createRange(Request $request)
    {

    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function delete(Request $request): JsonResponse
    {
        return $this->executeAction(
            fn () => $this->apiOkResponse($this->employeesService->deleteEmployeeWorkingHours($request->all()))
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request): JsonResponse
    {
        return $this->executeAction(
            fn () => $this->apiOkResponse($this->employeesService->updateEmployeeWorkingHours($request->all()))
        );
    }
}
