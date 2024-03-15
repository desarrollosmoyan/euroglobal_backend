<?php

namespace Support\Transformers;

use Carbon\Carbon;
use Domain\Gyms\Contracts\Services\GymsService;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract as Transformer;

class GymsServiceGymSubscriptionPaymentTransformer extends Transformer
{
    /**
     * @var array
     */
    protected array $availableIncludes = [
        'gymsServiceGymSubscriptionPaymentDetails',
        'order'
    ];

    /**
     * @var array
     */
    protected array $entityData = [];

    /**
     * @param int $id
     * @return array
     */
    public function transform(int $id): array
    {
        $entity = app(GymsService::class)->findGymSubscriptionPayment($id);

        $this->entityData = [
            'id' => $entity->id,
            'gym_subscription_id' => $entity->gym_subscription_id,
            'order_id' => $entity->order_id,
            'previous_expiration_date' => $entity->previous_expiration_date ? Carbon::parse($entity->previous_expiration_date)->toDateString() : $entity->previous_expiration_date,
            'next_expiration_date' => $entity->next_expiration_date ? Carbon::parse($entity->next_expiration_date)->toDateString() : $entity->next_expiration_date,
            'amount' => $entity->amount,
            'date' => $entity->date ? Carbon::parse($entity->date)->toDateString() : $entity->date,
            'created_by' => $entity->created_by,
            'last_modified_by' => $entity->last_modified_by,
            'created_at' => $entity->created_at,
            'updated_at' => $entity->updated_at
        ];

        return $this->entityData;
    }

    /**
     * @param int $id
     * @return Item|null
     */
    public function includeOrder(int $id): ?Item
    {
        return !empty($this->entityData['order_id']) ? $this->item(
            (int)$this->entityData['order_id'],
            app(OrdersServiceOrderTransformer::class)
        ) : null;
    }
}
