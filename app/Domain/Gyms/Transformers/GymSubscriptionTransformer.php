<?php

namespace Domain\Gyms\Transformers;

use Domain\Gyms\Models\GymSubscription;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract as Transformer;
use Support\Transformers\Traits\Client;
use Support\Transformers\Traits\CreatedByUser;
use Support\Transformers\Traits\GymFeeType;
use Support\Transformers\Traits\LastModifiedByUser;

class GymSubscriptionTransformer extends Transformer
{
    use CreatedByUser;
    use LastModifiedByUser;
    use Client;
    use GymFeeType;

    protected array $availableIncludes = [
        'createdByUser',
        'lastModifiedByUser',
        'client',
        'gymFeeType',
        'gymSubscriptionMembers',
        'gymSubscriptionNotes'
    ];

    /**
     * @param GymSubscription $entity
     * @return array
     */
    public function transform(GymSubscription $entity): array
    {
        return [
            'id' => $entity->id,
            'client_id' => $entity->client_id,
            'gym_fee_type_id' => $entity->gym_fee_type_id,
            'gym_fee_type_name' => $entity->gym_fee_type_name,
            'version' => $entity->version,
            'duration_number_of_days' => $entity->duration_number_of_days,
            'price' => $entity->price,
            'price_beneficiaries' => $entity->price_beneficiaries,
            'activation_date' => $entity->activation_date,
            'start_date' => $entity->start_date,
            'end_date' => $entity->end_date,
            'expiration_date' => $entity->expiration_date,
            'payment_day' => $entity->payment_day,
            'biweekly_payment_day' => $entity->biweekly_payment_day,
            'payment_type' => $entity->payment_type->value,
            'created_by' => $entity->created_by,
            'last_modified_by' => $entity->last_modified_by,
            'created_at' => $entity->created_at,
            'updated_at' => $entity->updated_at

        ];
    }

    /**
     * @param GymSubscription $entity
     * @return Collection|null
     */
    public function includeGymSubscriptionMembers(GymSubscription $entity): ?Collection
    {
        $gymSubscriptionMembers = $entity->gymSubscriptionMembers()->orderByDesc('id')->get();

        return $gymSubscriptionMembers ? $this->collection($gymSubscriptionMembers, app(GymSubscriptionMemberTransformer::class)) : null;
    }

    /**
     * @param GymSubscription $entity
     * @return Collection|null
     */
    public function includeGymSubscriptionNotes(GymSubscription $entity): ?Collection
    {
        $gymSubscriptionNotes = $entity->gymSubscriptionNotes()->orderByDesc('id')->get();

        return $gymSubscriptionNotes ? $this->collection($gymSubscriptionNotes, app(GymSubscriptionNoteTransformer::class)) : null;
    }
}
