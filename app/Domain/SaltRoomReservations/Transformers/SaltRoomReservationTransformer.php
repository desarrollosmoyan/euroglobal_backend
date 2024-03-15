<?php

namespace Domain\SaltRoomReservations\Transformers;

use Domain\SaltRoomReservations\Contracts\Repositories\SaltRoomReservationsRepository;
use Domain\SaltRoomReservations\Models\SaltRoomReservation;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract as Transformer;
use Support\Transformers\OrdersServiceOrderDetailTransformer;
use Support\Transformers\Traits\Client;
use Support\Transformers\Traits\CreatedByUser;
use Support\Transformers\Traits\LastModifiedByUser;

class SaltRoomReservationTransformer extends Transformer
{
    use Client;
    use CreatedByUser;
    use LastModifiedByUser;

    protected array $availableIncludes = [
        'client',
        'createdByUser',
        'lastModifiedByUser',
        'orderDetails'
    ];

    /**
     * @param SaltRoomReservation $entity
     * @return array
     */
    public function transform(SaltRoomReservation $entity): array
    {
        return [
            'id' => $entity->id,
            'client_id' => $entity->client_id,
            'date' => $entity->date,
            'time' => $entity->time,
            'duration' => $entity->duration,
            'adults' => $entity->adults,
            'children' => $entity->children,
            'used' => $entity->used,
            'notes' => $entity->notes,
            'schedule_note' => $entity->schedule_note,
            'treatment_reservations' => $entity->treatment_reservations,
            'notification_email' => $entity->notification_email,
            'created_by' => $entity->created_by,
            'last_modified_by' => $entity->last_modified_by,
            'created_at' => $entity->created_at,
            'updated_at' => $entity->updated_at
        ];
    }

    /**
     * @param SaltRoomReservation $entity
     * @return Collection|null
     */
    public function includeOrderDetails(SaltRoomReservation $entity): ?Collection
    {
        $ids = app(SaltRoomReservationsRepository::class)->relatedOrderDetails($entity->id)->pluck('order_detail_id')->values()->toArray();

        return count($ids) ? $this->collection($ids, app(OrdersServiceOrderDetailTransformer::class)) : null;
    }
}
