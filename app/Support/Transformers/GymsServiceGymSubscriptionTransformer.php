<?php

namespace Support\Transformers;

use Domain\Gyms\Contracts\Services\GymsService;
use Domain\Gyms\DataTransferObjects\GymSubscriptionNoteSearchRequest;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract as Transformer;
use Support\Transformers\GymsServiceGymSubscriptionNoteTransformer;

class GymsServiceGymSubscriptionTransformer extends Transformer
{
    /**
     * @var array
     */
    protected array $availableIncludes = [
        'client',
        'gymSubscriptionNotes'
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
        $entity = app(GymsService::class)->findGymSubscription($id);

        $this->entityData = [
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
            'payment_type' => $entity->payment_type,
            'created_at' => $entity->created_at,
            'updated_at' => $entity->updated_at
        ];

        return $this->entityData;
    }

    /**
     * @param int $id
     * @return Collection|null
     * @throws UnknownProperties
     */
    public function includeGymSubscriptionNotes(int $id): ?Collection
    {
        $notes = app(GymsService::class)->searchGymSubscriptionNotes(
            new GymSubscriptionNoteSearchRequest([
                'filters' => ['gym_subscription_id' => $id],
                'includes' => [],
                'paginate_size' => config('system.infinite_pagination')
            ])
        );

        return $notes->getData()->count() ? $this->collection(
            $notes->getData()->pluck('id')->values()->toArray(),
            app(GymsServiceGymSubscriptionNoteTransformer::class)
        ) : null;
    }
}
