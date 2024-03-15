<?php

namespace Domain\Gyms\Transformers;

use Domain\Gyms\Models\GymSubscriptionVersion;
use League\Fractal\TransformerAbstract as Transformer;
use Support\Transformers\Traits\Client;
use Support\Transformers\Traits\CreatedByUser;
use Support\Transformers\Traits\GymFeeType;
use Support\Transformers\Traits\GymSubscription;
use Support\Transformers\Traits\LastModifiedByUser;

class GymSubscriptionVersionTransformer extends Transformer
{
    use CreatedByUser;
    use LastModifiedByUser;
    use Client;
    use GymFeeType;
    use GymSubscription;

    protected array $availableIncludes = [
        'createdByUser',
        'lastModifiedByUser',
        'client',
        'gymFeeType',
        'gymSubscription'
    ];

    /**
     * @param GymSubscriptionVersion $entity
     * @return array
     */
    public function transform(GymSubscriptionVersion $entity): array
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
            'updated_at' => $entity->updated_at,
            'gym_subscription_id' => $entity->gym_subscription_id,

        ];
    }
}
