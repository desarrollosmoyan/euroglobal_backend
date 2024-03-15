<?php

namespace Domain\Gyms\Repositories;

use Domain\Gyms\Contracts\Repositories\GymSubscriptionNotesRepository as RepositoryInterface;
use Domain\Gyms\Models\GymSubscriptionNote;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Support\Core\Enums\SQLSort;
use Support\Models\Entity;
use Support\Repositories\Implementations\Repository;

class GymSubscriptionNotesRepository extends Repository implements RepositoryInterface
{
    //region Constructor And Fields

    /**
     * @var GymSubscriptionNote
     */
    private GymSubscriptionNote $entity;

    /**
     * @param GymSubscriptionNote $entity
     */
    public function __construct(GymSubscriptionNote $entity)
    {
        $this->entity = $entity;
    }

    //endregion

    //region RepositoryInterface">

    /**
     * @param array $filters
     * @param string $sortField
     * @param SQLSort $sortType
     * @return Collection
     */
    public function search(array $filters, string $sortField, SQLSort $sortType): Collection
    {
        return $this->searchQueryBuilder($filters, $sortField, $sortType)->get();
    }

    /**
     * @param array $filters
     * @param string $sortField
     * @param SQLSort $sortType
     * @return Builder
     */
    public function searchQueryBuilder(array $filters, string $sortField, SQLSort $sortType): Builder
    {
        $query = $this->getEntity()->newQuery()->select('*');

        if (array_key_exists('id', $filters)) {
            $query->whereIn('id', (array)$filters['id']);
        }

        if (array_key_exists('gym_subscription_id', $filters)) {
            $query->whereIn('gym_subscription_id', (array)$filters['gym_subscription_id']);
        }

        $query->orderBy($sortField, $sortType->value);

        return $query;
    }

    /**
     * @return GymSubscriptionNote
     */
    public function getEntity(): GymSubscriptionNote
    {
        return $this->entity;
    }

    /**
     * @param Entity $entity
     *
     * @return void
     */
    public function setEntity(Entity $entity): void
    {
        $this->entity = $entity;
    }

    //endregion
}
