<?php

namespace Domain\Gyms\DataTransferObjects;

use Support\DataTransferObjects\Entity;
use Support\DataTransferObjects\Traits\AuditUsers;
use Support\DataTransferObjects\Traits\Timestamps;

class GymSubscriptionNoteEntity extends Entity
{
    use AuditUsers;
    use Timestamps;

    public ?int $id;
    public int $gym_subscription_id;
    public string $note;

    public ?GymSubscriptionEntity $gymSubscription;
}
