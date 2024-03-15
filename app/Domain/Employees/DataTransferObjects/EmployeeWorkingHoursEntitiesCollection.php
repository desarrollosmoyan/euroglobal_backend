<?php

namespace Domain\Employees\DataTransferObjects;

use Support\DataTransferObjects\EntityCollection;

class EmployeeWorkingHoursEntitiesCollection extends EntityCollection
{
    public static function getEntityClass(): string
    {
        return EmployeeWorkingHoursEntity::class;
    }
}
