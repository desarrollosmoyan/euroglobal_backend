<?php

namespace Domain\Gyms\DataTransferObjects;

use Support\DataTransferObjects\SearchResponse;
use Support\Exceptions\InvalidDataTypeException;

class GymSubscriptionPaymentSearchResponse extends SearchResponse
{
    /**
     * @return GymSubscriptionPaymentEntitiesCollection
     */
    public function getData(): GymSubscriptionPaymentEntitiesCollection
    {
        return parent::getData();
    }

    /**
     * @throws InvalidDataTypeException
     */
    public function setData(mixed $data): static
    {
        if ($data === null || $data instanceof GymSubscriptionPaymentEntitiesCollection) {
            return parent::setData($data);
        }

        throw new InvalidDataTypeException('Passed data is not an instance of ' . GymSubscriptionPaymentEntitiesCollection::class);
    }
}
