<?php

namespace Domain\SaltRoomReservations\DataTransferObjects;

use Domain\Clients\DataTransferObjects\ClientEntity;
use Support\DataTransferObjects\Entity;
use Support\DataTransferObjects\Traits\AuditUsers;
use Support\DataTransferObjects\Traits\Timestamps;

class SaltRoomReservationEntity extends Entity
{
    use AuditUsers;
    use Timestamps;

    public ?int $id;
    public int $client_id;
    public ?string $date;
    public ?string $time;
    public ?int $duration;
    public int $adults;
    public int $children;
    public bool $used;
    public ?string $notes;
    public ?string $schedule_note;
    public ?string $notification_email;
    public ?int $treatment_reservations;

    public ?ClientEntity $client;
    public ?array $orderDetails;
}
