<?php

namespace Domain\SaltRoomReservations\Mails;

use Domain\SaltRoomReservations\Contracts\Services\SaltRoomReservationsService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailReservation extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * @param array $data
     * @param SaltRoomReservationsService $service
     */
    public function __construct(
        public readonly array $data,
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
        $record = $this->service->find($this->data['id'], ['client','orderDetails','orderDetails.order','orderDetails.product']);

        return $this->to($this->data['email'])
            ->subject('Reserva Salas de Sal - Balneario Thermas de Griñon')
            ->markdown('emails.salt_room_reservations.mail_reservation')
            ->with(['record' => $record]);
    }
}
