<?php

namespace Domain\Gyms\DataTransferObjects;

use Support\DataTransferObjects\EntityCollection;

class GymSubscriptionNoteEntitiesCollection extends EntityCollection
{
    public static function getEntityClass(): string
    {
        return GymSubscriptionNoteEntity::class;
    }
}