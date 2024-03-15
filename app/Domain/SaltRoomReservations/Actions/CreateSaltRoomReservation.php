<?php

namespace Domain\SaltRoomReservations\Actions;

use Closure;
use Domain\Festives\Contracts\Services\FestivesService;
use Domain\Festives\DataTransferObjects\FestiveSearchRequest;
use Domain\SaltRoomReservations\Contracts\Repositories\SaltRoomReservationOrderDetailsRepository;
use Domain\SaltRoomReservations\Contracts\Repositories\SaltRoomReservationsRepository;
use Domain\SaltRoomReservations\Models\SaltRoomReservation;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Validation\ValidationException;
use Support\Core\Enums\SQLSort;

class CreateSaltRoomReservation
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

        $record = $this->repository->add($data);

        if (!empty($data['order_detail_id'])) {
            $this->saltRoomReservationOrderDetailsRepository->add([
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
        $rules = [
            'client_id' => 'required|exists:clients,id',
            'order_detail_id' => 'nullable|exists:order_details,id',
            'date' => [
                'required',
                'date_format:Y-m-d'
            ],
            'time' => 'required|date_format:H:i',
            'duration' => 'required|numeric',
            'adults' => [
                'required',
                'numeric',
                'max:4',
                function (string $attribute, mixed $value, Closure $fail) use ($data) {
                    $reservations = $this->repository->search(
                        ['date' => $data['date'], 'time' => $data['time']],
                        'id',
                        SQLSort::SORT_ASC
                    );

                    $totalAdults = $value;

                    foreach ($reservations as $reservation) {
                        $totalAdults += $reservation->adults;
                    }

                    if ($totalAdults > 4) {
                        $fail('Revise las reservaciones en este horario. El número máximo de adultos en la habitación no puede exceder 4.');
                    }
                },
            ],
            'children' =>
            [
                'required',
                'numeric',
                'max:4',
                function (string $attribute, mixed $value, Closure $fail) use ($data) {
                    $reservations = $this->repository->search(
                        ['date' => $data['date'], 'time' => $data['time']],
                        'id',
                        SQLSort::SORT_ASC
                    );

                    $totalChildrens = $value;

                    foreach ($reservations as $reservation) {
                        $totalChildrens += $reservation->children;
                    }

                    if ($totalChildrens > 4) {
                        $fail('Revise las reservaciones en este horario. El número máximo de menores en la habitación no puede exceder 4.');
                    }

                },
            ],
            'used' => 'required|boolean',
            'treatment_reservations' => 'nullable|numeric'
        ];

        if (!array_key_exists('ignore_festive_validation', $data)) {
            $rules['date'][] = function ($attribute, $value, $fail) use ($data) {
                $festiveRecords = $this->festivesService->search(
                    new FestiveSearchRequest([
                        'filters' => ['date' => $value],
                        'paginateSize' => config('system.infinite_pagination')
                    ])
                )->getData();
                $time = $data['time'];
                if ($festiveRecords->count()) {
                    foreach ($festiveRecords as $festive) {
                        if (
                            $festive->type === 'Día Completo'
                            ||
                            in_array($time, $festive->closed_hours)
                        ) {
                            $fail('Date and time not available to reserve.');
                        }
                    }
                }
            };
        }

        return $rules;
    }
}
