<?php

namespace Domain\SaltRoomReservations\Actions;

use Domain\SaltRoomReservations\Contracts\Repositories\SaltRoomReservationsRepository;
use Domain\SaltRoomReservations\Models\SaltRoomReservation;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

class MarkAsUsedSaltRoomReservation
{
    private SaltRoomReservationsRepository $repository;
    private Factory $validatorFactory;

    /**
     * @param SaltRoomReservationsRepository $repository
     * @param Factory $validatorFactory
     */
    public function __construct(
        SaltRoomReservationsRepository $repository,
        Factory $validatorFactory
    ) {
        $this->repository = $repository;
        $this->validatorFactory = $validatorFactory;
    }

    /**
     * @param array $data
     * @return SaltRoomReservation
     * @throws ValidationException
     */
    public function __invoke(array $data): SaltRoomReservation
    {
        $validator = $this->validatorFactory->make($data, $this->rules());

        $validator->validate();

        return $this->repository->edit(
            Arr::only($data, ['id', 'used'])
        );
    }

    /**
     * @return array
     */
    private function rules(): array
    {
        return [
            'id' => 'required|exists:salt_room_reservations,id',
            'used' => 'required|boolean'
        ];
    }
}
