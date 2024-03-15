<?php

namespace Domain\Orders\DataTransferObjects;

use Support\Core\Enums\SQLSort;
use Support\DataTransferObjects\SearchRequest;

class OrderSummarizedProductionSearchRequest extends SearchRequest
{
    public string $sortField = 'product_name';
    public SQLSort $sortType = SQLSort::SORT_ASC;
}
