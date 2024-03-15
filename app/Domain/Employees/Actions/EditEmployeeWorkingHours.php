<?php

namespace Domain\Employees\Actions;

use Domain\Employees\Contracts\Repositories\EmployeeWorkingHoursRepository;
use Domain\Employees\Models\EmployeeWorkingHours;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Validation\ValidationException;

class EditEmployeeWorkingHours
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
     * @throws ValidationException
     */
    public function __invoke(array $data): EmployeeWorkingHours
    {
        $validator = $this->validatorFactory->make($data, $this->rules());

        $validator->validate();

        return $this->repository->edit($data);
    }

    /**
     * @return string[]
     */
    private function rules(): array
    {
        return [
            'id' => 'required|exists:employee_working_hours',
            'employee_id' => 'required',
            'date' => 'required|date_format:Y-m-d',
            'work_schedule' => 'required|array',
            'work_schedule.*.start' => 'required|date_format:H:i',
            'work_schedule.*.end' => 'required|date_format:H:i'
        ];
    }
}
