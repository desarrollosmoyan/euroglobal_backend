<?php

namespace Domain\Employees\Actions;


use Domain\Employees\Models\EmployeeWorkingHours;
use Domain\Employees\Repositories\EmployeeWorkingHoursRepository;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Validation\ValidationException;
use Support\Exceptions\DatabaseException;

class DeleteEmployeeWorkingHours
{
    private EmployeeWorkingHoursRepository $repository;
    private Factory $validatorFactory;

    /**
     * @param EmployeeWorkingHoursRepository $repository
     * @param Factory $validatorFactory
     */
    public function __construct(
        EmployeeWorkingHoursRepository $repository,
        Factory $validatorFactory
    ) {
        $this->repository = $repository;
        $this->validatorFactory = $validatorFactory;
    }

    /**
     * @param array $data
     * @return EmployeeWorkingHours
     * @throws ValidationException|DatabaseException
     */
    public function __invoke(array $data): EmployeeWorkingHours
    {
        $validator = $this->validatorFactory->make($data, $this->rules($data));

        $validator->validate();

        return $this->repository->delete($data);
    }

    /**
     * @param $data
     * @return array
     */
    private function rules($data): array
    {
        return ['id' => 'required|exists:employee_working_hours'];
    }
}
