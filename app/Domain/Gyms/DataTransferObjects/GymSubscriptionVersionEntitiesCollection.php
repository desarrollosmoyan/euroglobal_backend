<?php

namespace Domain\Gyms\DataTransferObjects;

use Support\DataTransferObjects\EntityCollection;

class GymSubscriptionVersionEntitiesCollection extends EntityCollection
{
    public static function getEntityClass(): string
    {
        return GymSubscriptionVersionEntity::class;
    }
}
