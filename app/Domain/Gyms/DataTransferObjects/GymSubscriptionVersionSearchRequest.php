<?php

namespace Domain\Gyms\DataTransferObjects;

use Support\Core\Enums\SQLSort;
use Support\DataTransferObjects\SearchRequest;

class GymSubscriptionVersionSearchRequest extends SearchRequest
{
    public string $sortField = 'id';
    public SQLSort $sortType = SQLSort::SORT_DESC;
}
