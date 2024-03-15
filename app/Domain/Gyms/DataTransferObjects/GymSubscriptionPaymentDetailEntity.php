<?php

namespace Domain\Gyms\DataTransferObjects;

use Support\DataTransferObjects\Entity;
use Support\DataTransferObjects\Traits\AuditUsers;
use Support\DataTransferObjects\Traits\Timestamps;

class GymSubscriptionPaymentDetailEntity extends Entity
{
    use AuditUsers;
    use Timestamps;

    public ?int $id;
    public ?int $idjj;
    public int $gym_subscription_payment_id;
    public string $type;
    public ?int $gym_subscription_member_id;
    public float $price;
    public int $quantity;
    public float $amount;

    public ?GymSubscriptionPaymentEntity $gymSubscriptionPayment;
    public ?GymSubscriptionMemberEntity $gymSubscriptionMember;
}
