<?php

namespace Domain\Gyms\DataTransferObjects;

use Support\DataTransferObjects\SearchResponse;
use Support\Exceptions\InvalidDataTypeException;

class GymSubscriptionPaymentDetailSearchResponse extends SearchResponse
{
    /**
     * @return GymSubscriptionPaymentDetailEntitiesCollection
     */
    public function getData(): GymSubscriptionPaymentDetailEntitiesCollection
    {
        return parent::getData();
    }

    /**
     * @throws InvalidDataTypeException
     */
    public function setData(mixed $data): static
    {
        if ($data === null || $data instanceof GymSubscriptionPaymentDetailEntitiesCollection) {
            return parent::setData($data);
        }

        throw new InvalidDataTypeException('Passed data is not an instance of ' . GymSubscriptionPaymentDetailEntitiesCollection::class);
    }
}
