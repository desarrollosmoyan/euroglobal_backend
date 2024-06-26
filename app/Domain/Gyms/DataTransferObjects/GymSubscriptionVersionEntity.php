<?php

namespace Domain\Gyms\DataTransferObjects;

use Domain\Clients\DataTransferObjects\ClientEntity;
use Support\DataTransferObjects\Entity;
use Support\DataTransferObjects\Traits\AuditUsers;
use Support\DataTransferObjects\Traits\Timestamps;

class GymSubscriptionVersionEntity extends Entity
{
    use AuditUsers;
    use Timestamps;

    public ?int $id;
    public ?int $client_id;
    public ?int $gym_fee_type_id;
    public ?string $gym_fee_type_name;
    public ?int $version;
    public ?int $duration_number_of_days;
    public ?string $price;
    public ?string $price_beneficiaries;
    public ?string $activation_date;
    public ?string $start_date;
    public ?string $end_date;
    public ?string $expiration_date;
    public ?int $payment_day;
    public ?int $biweekly_payment_day;
    public ?string $payment_type;
    public ?int $gym_subscription_id;

    public ?ClientEntity $client;
    public ?GymFeeTypeEntity $gymFeeType;
    public ?GymSubscriptionEntity $gymSubscription;
}
