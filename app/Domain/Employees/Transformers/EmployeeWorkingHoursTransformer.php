<?php

namespace Domain\Employees\Transformers;

use Domain\Employees\Models\EmployeeWorkingHours;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract as Transformer;
use Support\Transformers\Traits\CreatedByUser;
use Support\Transformers\Traits\LastModifiedByUser;

class EmployeeWorkingHoursTransformer extends Transformer
{
    use CreatedByUser;
    use LastModifiedByUser;

    protected array $availableIncludes = [
        'createdByUser',
        'lastModifiedByUser',
        'employee'
    ];

    /**
     * @param EmployeeWorkingHours $entity
     * @return array
     */
    public function transform(EmployeeWorkingHours $entity): array
    {
        return [
            'id' => $entity->id,
            'employee_id' => $entity->employee_id,
            'date' => $entity->date,
            'work_schedule' => $entity->work_schedule,
            'created_by' => $entity->created_by,
            'last_modified_by' => $entity->last_modified_by,
            'created_at' => $entity->created_at,
            'updated_at' => $entity->updated_at
        ];
    }

    /**
     * @param EmployeeWorkingHours $entity
     * @return Item|null
     */
    public function includeEmployee(EmployeeWorkingHours $entity): ?Item
    {
        $employee = $entity->employee;

        return $employee ? $this->item($employee, app(EmployeeTransformer::class)) : null;
    }
}
