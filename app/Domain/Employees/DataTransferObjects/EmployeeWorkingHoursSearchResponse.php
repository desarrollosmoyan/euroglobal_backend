<?php

namespace Domain\Employees\DataTransferObjects;

use Support\DataTransferObjects\SearchResponse;
use Support\Exceptions\InvalidDataTypeException;

class EmployeeWorkingHoursSearchResponse extends SearchResponse
{
    /**
     * @return EmployeeWorkingHoursEntitiesCollection
     */
    public function getData(): EmployeeWorkingHoursEntitiesCollection
    {
        return parent::getData();
    }

    /**
     * @throws InvalidDataTypeException
     */
    public function setData(mixed $data): static
    {
        if ($data === null || $data instanceof EmployeeWorkingHoursEntitiesCollection) {
            return parent::setData($data);
        }

        throw new InvalidDataTypeException('Passed data is not an instance of ' . EmployeeWorkingHoursEntitiesCollection::class);
    }
}
