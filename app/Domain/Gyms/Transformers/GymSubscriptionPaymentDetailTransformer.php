<?php

namespace Domain\Gyms\Transformers;

use Domain\Gyms\Models\GymSubscriptionPaymentDetail;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract as Transformer;
use Support\Transformers\Traits\CreatedByUser;
use Support\Transformers\Traits\LastModifiedByUser;

class GymSubscriptionPaymentDetailTransformer extends Transformer
{
    use CreatedByUser;
    use LastModifiedByUser;

    protected array $availableIncludes = [
        'createdByUser',
        'lastModifiedByUser',
        'gymSubscriptionMember',
        'gymSubscriptionPayment',
    ];

    /**
     * @param GymSubscriptionPaymentDetail $entity
     * @return array
     */
    public function transform(GymSubscriptionPaymentDetail $entity): array
    {
        return [
            'id' => $entity->id,
            'gym_subscription_payment_id' => $entity->gym_subscription_payment_id,
            'type' => $entity->type->value,
            'gym_subscription_member_id' => $entity->gym_subscription_member_id,
            'price' => $entity->price,
            'quantity' => $entity->quantity,
            'amount' => $entity->amount,
            'created_by' => $entity->created_by,
            'last_modified_by' => $entity->last_modified_by,
            'created_at' => $entity->created_at,
            'updated_at' => $entity->updated_at
        ];
    }

    /**
     * @param GymSubscriptionPaymentDetail $entity
     * @return Item|null
     */
    public function includeGymSubscriptionMember(GymSubscriptionPaymentDetail $entity): ?Item
    {
        return empty($entity->gym_subscription_member_id) ? null : $this->item($entity->gymSubscriptionMember, app(GymSubscriptionMemberTransformer::class));
    }

    /**
     * @param GymSubscriptionPaymentDetail $entity
     * @return Item|null
     */
    public function includeGymSubscriptionPayment(GymSubscriptionPaymentDetail $entity): ?Item
    {
        return $this->item($entity->gymSubscriptionPayment, app(GymSubscriptionPaymentTransformer::class));
    }
}
