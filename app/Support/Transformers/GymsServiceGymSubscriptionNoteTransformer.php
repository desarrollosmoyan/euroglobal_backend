<?php

namespace Support\Transformers;

use Domain\Gyms\Contracts\Services\GymsService;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract as Transformer;

class GymsServiceGymSubscriptionNoteTransformer extends Transformer
{
    /**
     * @var array|string[]
     */
    protected array $availableIncludes = [
        'gymSubscription',
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
        $entity = app(GymsService::class)->findNote($id);

        $this->entityData = [
            'id' => $entity->id,
            'gym_subscription_id' => $entity->gym_subscription_id,
            'note' => $entity->note,
            'created_at' => $entity->created_at,
            'updated_at' => $entity->updated_at
        ];

        return $this->entityData;
    }

    /**
     * @param int $id
     * @return Item|null
     */
    public function includeGymSubscription(int $id): ?Item
    {
        return !empty($this->entityData['gym_subscription_id']) ? $this->item(
            (int)$this->entityData['gym_subscription_id'],
            app(GymsServiceGymSubscriptionTransformer::class)
        ) : null;
    }
}
