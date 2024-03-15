<?php

namespace Support\Transformers;

use Domain\SaltRoomReservations\Contracts\Services\SaltRoomReservationsService;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract as Transformer;

class SaltRoomReservationsServiceSaltRoomReservationTransformer extends Transformer
{
    protected array $availableIncludes = [
        'client'
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
        $entity = app(SaltRoomReservationsService::class)->find($id);

        $this->entityData = [
            'id' => $entity->id,
            'client_id' => $entity->client_id,
            'date' => $entity->date,
            'time' => $entity->time,
            'duration' => $entity->duration,
            'adults' => $entity->adults,
            'children' => $entity->children,
            'used' => $entity->used,
            'notes' => $entity->notes,
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
    public function includeClient(int $id): ?Item
    {
        return !empty($this->entityData['client_id']) ? $this->item(
            (int)$this->entityData['client_id'],
            app(ClientsServiceClientTransformer::class)
        ) : null;
    }
}
