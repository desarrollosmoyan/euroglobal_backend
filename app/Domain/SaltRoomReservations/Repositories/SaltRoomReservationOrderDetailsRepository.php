<?php

namespace Domain\SaltRoomReservations\Repositories;

use Domain\SaltRoomReservations\Contracts\Repositories\SaltRoomReservationOrderDetailsRepository as RepositoryInterface;
use Domain\SaltRoomReservations\Models\SaltRoomReservationOrderDetail;
use Support\Models\Entity;
use Support\Repositories\Implementations\Repository;

class SaltRoomReservationOrderDetailsRepository extends Repository implements RepositoryInterface
{
    //region Constructor And Fields

    /**
     * @var SaltRoomReservationOrderDetail
     */
    private SaltRoomReservationOrderDetail $entity;

    /**
     * @param SaltRoomReservationOrderDetail $entity
     */
    public function __construct(SaltRoomReservationOrderDetail $entity)
    {
        $this->entity = $entity;
    }

    //endregion

    //region RepositoryInterface">

    /**
     * @return SaltRoomReservationOrderDetail
     */
    public function getEntity(): SaltRoomReservationOrderDetail
    {
        return $this->entity;
    }

    /**
     * @param Entity $entity
     *
     * @return void
     */
    public function setEntity(Entity $entity): void
    {
        $this->entity = $entity;
    }

    //endregion
}
