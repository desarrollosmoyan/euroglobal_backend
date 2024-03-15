<?php

namespace Domain\Gyms\DataTransferObjects;

use Support\DataTransferObjects\SearchResponse;
use Support\Exceptions\InvalidDataTypeException;

class GymSubscriptionVersionSearchResponse extends SearchResponse
{
    /**
     * @return GymSubscriptionVersionEntitiesCollection
     */
    public function getData(): GymSubscriptionVersionEntitiesCollection
    {
        return parent::getData();
    }

    /**
     * @throws InvalidDataTypeException
     */
    public function setData(mixed $data): static
    {
        if ($data === null || $data instanceof GymSubscriptionVersionEntitiesCollection) {
            return parent::setData($data);
        }

        throw new InvalidDataTypeException('Passed data is not an instance of ' . GymSubscriptionVersionEntitiesCollection::class);
    }
}
