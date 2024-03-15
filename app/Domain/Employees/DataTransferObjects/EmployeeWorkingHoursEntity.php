<?php

namespace Domain\Employees\DataTransferObjects;

use Support\DataTransferObjects\Entity;
use Support\DataTransferObjects\Traits\AuditUsers;
use Support\DataTransferObjects\Traits\Timestamps;

class EmployeeWorkingHoursEntity extends Entity
{
    use AuditUsers;
    use Timestamps;

    public ?int $id;
    public int $employee_id;
    public string $date;
    public ?array $work_schedule;

    public ?EmployeeEntity $employee;
}
