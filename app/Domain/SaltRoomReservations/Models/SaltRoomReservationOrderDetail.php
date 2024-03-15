<?php

namespace Domain\SaltRoomReservations\Models;

use OwenIt\Auditing\Contracts\Auditable;
use Support\Models\Entity;

class SaltRoomReservationOrderDetail extends Entity implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $table = 'salt_room_reservations_order_details';

    protected $fillable = [
        'id',
        'order_detail_id',
    ];
}
