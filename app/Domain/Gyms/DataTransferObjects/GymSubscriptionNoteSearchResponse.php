<?php

namespace Domain\Gyms\DataTransferObjects;

use Support\DataTransferObjects\SearchResponse;
use Support\Exceptions\InvalidDataTypeException;

class GymSubscriptionNoteSearchResponse extends SearchResponse
{
    /**
     * @return GymSubscriptionNoteEntitiesCollection
     */
    public function getData(): GymSubscriptionNoteEntitiesCollection
    {
        return parent::getData();
    }

    /**
     * @throws InvalidDataTypeException
     */
    public function setData(mixed $data): static
    {
        if ($data === null || $data instanceof GymSubscriptionNoteEntitiesCollection) {
            return parent::setData($data);
        }

        throw new InvalidDataTypeException('Passed data is not an instance of ' . GymSubscriptionNoteEntitiesCollection::class);
    }
}
