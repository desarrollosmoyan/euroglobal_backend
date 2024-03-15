<?php

namespace Domain\SaltRoomReservations\Mails;

use Domain\SaltRoomReservations\Contracts\Services\SaltRoomReservationsService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UpcomingReservation extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param int $id
     * @param SaltRoomReservationsService $service
     */
    public function __construct(
        public readonly int $id,
        private readonly SaltRoomReservationsService $service
    ) {
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): static
    {
        $record = $this->service->find($this->id, ['client']);

//        return $this->to($record->client->email)
        return $this->to('info@thermasdegrinon.com')
            ->subject('Reserva Salas de Sal - Balneario Thermas de Griñon')
            ->markdown('emails.salt_room_reservations.upcoming_reservation')
            ->with(['record' => $record]);
    }
}
