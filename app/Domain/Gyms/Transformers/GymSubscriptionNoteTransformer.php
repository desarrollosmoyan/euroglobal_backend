<?php

namespace Domain\Gyms\Transformers;

use Domain\Gyms\Models\GymSubscriptionNote;
use Domain\Gyms\Transformers\GymSubscriptionTransformer;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract as Transformer;
use Support\Transformers\Traits\CreatedByUser;
use Support\Transformers\Traits\LastModifiedByUser;

class GymSubscriptionNoteTransformer extends Transformer
{
    use CreatedByUser;
    use LastModifiedByUser;

    protected array $availableIncludes = [
        'createdByUser',
        'lastModifiedByUser',
        'gymSubscription',
    ];

    /**
     * @param GymSubscriptionNote $entity
     * @return array
     */
    public function transform(GymSubscriptionNote $entity): array
    {
        return [
            'id' => $entity->id,
            'gym_subscription_id' => $entity->gym_subscription_id,
            'note' => $entity->note,
            'created_by' => $entity->created_by,
            'last_modified_by' => $entity->last_modified_by,
            'created_at' => $entity->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $entity->updated_at->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * @param GymSubscriptionNote $entity
     * @return Item|null
     */
    public function includeGymSubscription(GymSubscriptionNote $entity): ?Item
    {
        $gymSubscription = $entity->gymSubscription;

        return $gymSubscription ? $this->item($gymSubscription, app(GymSubscriptionTransformer::class)) : null;
    }
}
