<?php

namespace Domain\SaltRoomReservations\Actions;

use Domain\SaltRoomReservations\Contracts\Repositories\SaltRoomReservationOrderDetailsRepository;
use Domain\SaltRoomReservations\Contracts\Repositories\SaltRoomReservationsRepository;
use Domain\SaltRoomReservations\Models\SaltRoomReservation;
use Domain\Festives\Contracts\Services\FestivesService;
use Domain\Festives\DataTransferObjects\FestiveSearchRequest;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Validation\ValidationException;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class UpdateSaltRoomReservation
{
    /**
     * @param SaltRoomReservationsRepository $repository
     * @param SaltRoomReservationOrderDetailsRepository $saltRoomReservationOrderDetailsRepository
     * @param FestivesService $festivesService
     * @param Factory $validatorFactory
     */
    public function __construct(
        private readonly SaltRoomReservationsRepository $repository,
        private readonly SaltRoomReservationOrderDetailsRepository $saltRoomReservationOrderDetailsRepository,
        private readonly FestivesService $festivesService,
        private readonly Factory $validatorFactory
    ) {
    }

    /**
     * @param array $data
     * @return SaltRoomReservation
     * @throws ValidationException
     */
    public function __invoke(array $data): SaltRoomReservation
    {
        $validator = $this->validatorFactory->make($data, $this->rules($data));

        $validator->validate();

        $record = $this->repository->edit($data);

        if (!empty($data['order_detail_id'])) {
            $this->saltRoomReservationOrderDetailsRepository->firstOrCreate([
                'id' => $record->id,
                'order_detail_id' => $data['order_detail_id']
            ]);
        }

        return $record;
    }

    /**
     * @param array $data
     * @return array
     */
    private function rules(array $data): array
    {
        return [
            'id' => 'required|exists:salt_room_reservations,id',
            'order_detail_id' => 'nullable|exists:order_details,id',
            'client_id' => 'required|exists:clients,id',
            'date' => [
                'required',
                'date_format:Y-m-d',
//                function ($attribute, $value, $fail) use ($data) {
//                    $festiveRecords = $this->festivesService->search(
//                        new FestiveSearchRequest([
//                            'filters' => ['date' => $value],
//                            'paginateSize' => config('system.infinite_pagination')
//                        ])
//                    )->getData();
//                    $time = $data['time'];
//                    if ($festiveRecords->count()) {
//                        foreach ($festiveRecords as $festive) {
//                            if (
//                                $festive->type === 'Día Completo'
//                                ||
//                                in_array($time, $festive->closed_hours)
//                            ) {
//                                $fail('Date and time not available to reserve.');
//                            }
//                        }
//                    }
//                },
            ],
            'time' => 'required|date_format:H:i',
            'duration' => 'required|numeric',
            'adults' => 'required|numeric',
            'children' => 'required|numeric',
            'used' => 'required|boolean',
            'treatment_reservations' => 'nullable|numeric'
        ];
    }
}
