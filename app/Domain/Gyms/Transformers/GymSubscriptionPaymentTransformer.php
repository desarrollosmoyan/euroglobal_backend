<?php

namespace Domain\Gyms\Transformers;

use Carbon\Carbon;
use Domain\Gyms\Models\GymSubscriptionPayment;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract as Transformer;
use Support\Transformers\Traits\CreatedByUser;
use Support\Transformers\Traits\LastModifiedByUser;
use Support\Transformers\Traits\Order;

class GymSubscriptionPaymentTransformer extends Transformer
{
    use CreatedByUser;
    use LastModifiedByUser;
    use Order;

    protected array $availableIncludes = [
        'createdByUser',
        'lastModifiedByUser',
        'gymSubscriptionPaymentDetails',
        'order'
    ];

    /**
     * @param GymSubscriptionPayment $entity
     * @return Collection|null
     */
    public function includeGymSubscriptionPaymentDetails(GymSubscriptionPayment $entity): ?Collection
    {
        $records = $entity->gymSubscriptionPaymentDetails;

        return $records ? $this->collection($records, app(GymSubscriptionPaymentDetailTransformer::class)) : null;
    }

    /**
     * @param GymSubscriptionPayment $entity
     * @return array
     */
    public function transform(GymSubscriptionPayment $entity): array
    {
        return [
            'id' => $entity->id,
            'gym_subscription_id' => $entity->gym_subscription_id,
            'order_id' => $entity->order_id,
            'previous_expiration_date' => $entity->previous_expiration_date ? Carbon::parse(
                $entity->previous_expiration_date
            )->toDateString() : $entity->previous_expiration_date,
            'next_expiration_date' => $entity->next_expiration_date ? Carbon::parse(
                $entity->next_expiration_date
            )->toDateString() : $entity->next_expiration_date,
            'amount' => $entity->amount,
            'date' => $entity->date ? Carbon::parse($entity->date)->toDateString() : $entity->date,
            'created_by' => $entity->created_by,
            'last_modified_by' => $entity->last_modified_by,
            'created_at' => $entity->created_at,
            'updated_at' => $entity->updated_at
        ];
    }
}
