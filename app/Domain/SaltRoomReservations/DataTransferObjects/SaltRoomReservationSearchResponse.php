<?php

namespace Domain\SaltRoomReservations\DataTransferObjects;

use Support\DataTransferObjects\SearchResponse;
use Support\Exceptions\InvalidDataTypeException;

class SaltRoomReservationSearchResponse extends SearchResponse
{
    /**
     * @return SaltRoomReservationEntitiesCollection
     */
    public function getData(): SaltRoomReservationEntitiesCollection
    {
        return parent::getData();
    }

    /**
     * @throws InvalidDataTypeException
     */
    public function setData(mixed $data): static
    {
        if ($data === null || $data instanceof SaltRoomReservationEntitiesCollection) {
            return parent::setData($data);
        }

        throw new InvalidDataTypeException(
            'Passed data is not an instance of ' . SaltRoomReservationEntitiesCollection::class
        );
    }
}
