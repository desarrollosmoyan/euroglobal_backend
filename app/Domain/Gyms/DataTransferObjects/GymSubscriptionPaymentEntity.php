<?php

namespace Domain\Gyms\DataTransferObjects;

use Domain\Orders\DataTransferObjects\OrderEntity;
use Support\DataTransferObjects\Entity;
use Support\DataTransferObjects\Traits\AuditUsers;
use Support\DataTransferObjects\Traits\Timestamps;

class GymSubscriptionPaymentEntity extends Entity
{
    use AuditUsers;
    use Timestamps;

    public ?int $id;
    public int $gym_subscription_id;
    public ?string $previous_expiration_date;
    public string $next_expiration_date;
    public float $amount;
    public string $date;

    public ?array $gymSubscriptionPaymentDetails;
    public ?OrderEntity $order;
}
