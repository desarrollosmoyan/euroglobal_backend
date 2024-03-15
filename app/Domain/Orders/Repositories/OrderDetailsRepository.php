<?php

namespace Domain\Orders\Repositories;

use Domain\Orders\Contracts\Repositories\OrderDetailsRepository as RepositoryInterface;
use Domain\Orders\Models\OrderDetail;
use Domain\Orders\Models\OrderDetailsCircuitReservations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Support\Core\Enums\SQLSort;
use Support\Models\Entity;
use Support\Repositories\Implementations\Repository;

class OrderDetailsRepository extends Repository implements RepositoryInterface
{
    //region Constructor And Fields

    /**
     * @var OrderDetail
     */
    private OrderDetail $entity;

    /**
     * @param OrderDetail $entity
     */
    public function __construct(OrderDetail $entity)
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

    public function summarized(array $filters, string $sortField, SQLSort $sortType): Collection
    {
        $query = $this->getEntity()->newQuery()->select(
            DB::raw('order_details.product_name, SUM(order_details.quantity) as quantity, SUM(order_details.price * order_details.quantity) as amount')
        );

        if (array_key_exists('created_at_from', $filters) && !empty($filters['created_at_from'])) {
            $query->where('order_details.created_at', '>=', $filters['created_at_from']);
        }

        if (array_key_exists('created_at_to', $filters) && !empty($filters['created_at_to'])) {
            $query->where('order_details.created_at', '<=', $filters['created_at_to']);
        }

        if (array_key_exists('company_id', $filters) && !empty($filters['company_id'])) {
            $query->join('orders', 'orders.id', '=', 'order_details.order_id');
            $query->whereIn('orders.company_id', (array)$filters['company_id']);
        }

        if (array_key_exists('product_name', $filters) && !empty($filters['product_name'])) {
            $query->where('order_details.product_name', 'LIKE', '%' . $filters['product_name'] . '%');
        }

        if (array_key_exists('product_type_id', $filters) && !empty($filters['product_type_id'])) {
            $query->join('products', 'products.id', '=', 'order_details.product_id');
            $query->whereIn('products.product_type_id', (array)$filters['product_type_id']);
        }

        if (array_key_exists('product_id', $filters) && !empty($filters['product_id'])) {
            $query->whereIn('order_details.product_id', (array)$filters['product_id']);
        }

        return $query->groupBy('order_details.product_name')
            ->orderBy('order_details.' . $sortField, $sortType->value)
            ->get();
    }

    /**
     * @param array $filters
     * @param string $sortField
     * @param SQLSort $sortType
     * @return Builder
     */
    public function searchQueryBuilder(array $filters, string $sortField, SQLSort $sortType): Builder
    {
        $query = $this->getEntity()->newQuery()->select('order_details.*');

        if (array_key_exists('id', $filters) && !empty($filters['id'])) {
            $query->whereIn('order_details.id', (array)$filters['id']);
        }

        if (array_key_exists('order_id', $filters) && !empty($filters['order_id'])) {
            $query->whereIn('order_details.order_id', (array)$filters['order_id']);
        }

        if (array_key_exists('product_id', $filters) && !empty($filters['product_id'])) {
            $query->whereIn('order_details.product_id', (array)$filters['product_id']);
        }

        if (array_key_exists('created_at_from', $filters) && !empty($filters['created_at_from'])) {
            $query->where('order_details.created_at', '>=', $filters['created_at_from']);
        }

        if (array_key_exists('created_at_to', $filters) && !empty($filters['created_at_to'])) {
            $query->where('order_details.created_at', '<=', $filters['created_at_to']);
        }

        if (array_key_exists('company_id', $filters) && !empty($filters['company_id'])) {
            $query->join('orders', 'orders.id', '=', 'order_details.order_id');
            $query->whereIn('orders.company_id', (array)$filters['company_id']);
        }

        $query->orderBy('order_details.' . $sortField, $sortType->value);

        return $query;
    }

    /**
     * @return OrderDetail
     */
    public function getEntity(): OrderDetail
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
