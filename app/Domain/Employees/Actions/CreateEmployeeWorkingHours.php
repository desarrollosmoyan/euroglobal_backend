<?php

namespace Domain\Employees\Actions;

use Carbon\CarbonPeriod;
use Domain\Employees\Contracts\Repositories\EmployeeWorkingHoursRepository;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class CreateEmployeeWorkingHours
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
     * @return Collection
     * @throws ValidationException
     */
    public function __invoke(array $data): Collection
    {
        $validator = $this->validatorFactory->make($data, $this->rules());

        $validator->validate();

        $results = collect();

        $period = CarbonPeriod::create($data['date_from'], $data['date_to']);

        foreach ($period as $date) {
            $results->push(
                $this->repository->add([
                    'employee_id' => $data['employee_id'],
                    'date' => $date->format('Y-m-d'),
                    'work_schedule' => $data['work_schedule']
                ])
            );
        }

        return $results;
    }

    /**
     * @return string[]
     */
    private function rules(): array
    {
        return [
            'employee_id' => 'required',
            'date_from' => 'required|date_format:Y-m-d',
            'date_to' => 'required|date_format:Y-m-d',
            'work_schedule' => 'required|array',
            'work_schedule.*.start' => 'required|date_format:H:i',
            'work_schedule.*.end' => 'required|date_format:H:i'
        ];
    }
}
