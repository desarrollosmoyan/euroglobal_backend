<?php

namespace Domain\SaltRoomReservations\DataTransferObjects;

use Support\DataTransferObjects\EntityCollection;

class SaltRoomReservationEntitiesCollection extends EntityCollection
{
    public static function getEntityClass(): string
    {
        return SaltRoomReservationEntity::class;
    }
}
