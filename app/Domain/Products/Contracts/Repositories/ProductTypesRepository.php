<?php

namespace Domain\Products\Contracts\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Support\Core\Enums\SQLSort;
use Support\Repositories\Contracts\Repository;

interface ProductTypesRepository extends Repository
{
    public function search(array $filters, string $sortField, SQLSort $sortType): Collection;

    public function searchQueryBuilder(array $filters, string $sortField, SQLSort $sortType): Builder;
}
