<?php

namespace Domain\Gyms\DataTransferObjects;

use Support\DataTransferObjects\EntityCollection;

class GymSubscriptionPaymentEntitiesCollection extends EntityCollection
{
    public static function getEntityClass(): string
    {
        return GymSubscriptionPaymentEntity::class;
    }
}
