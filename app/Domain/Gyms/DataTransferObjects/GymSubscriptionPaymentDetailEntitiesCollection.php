<?php

namespace Domain\Gyms\DataTransferObjects;

use Support\DataTransferObjects\EntityCollection;

class GymSubscriptionPaymentDetailEntitiesCollection extends EntityCollection
{
    public static function getEntityClass(): string
    {
        return GymSubscriptionPaymentDetailEntity::class;
    }
}
