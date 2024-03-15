<?php

namespace Domain\SaltRoomReservations\DataTransferObjects;

use Support\Core\Enums\SQLSort;
use Support\DataTransferObjects\SearchRequest;

class SaltRoomReservationSearchRequest extends SearchRequest
{
    public string $sortField = 'id';
    public SQLSort $sortType = SQLSort::SORT_DESC;
}
