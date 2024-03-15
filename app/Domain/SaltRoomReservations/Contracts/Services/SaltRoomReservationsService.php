<?php

namespace Domain\SaltRoomReservations\Contracts\Services;

use Domain\SaltRoomReservations\DataTransferObjects\SaltRoomReservationEntity;
use Domain\SaltRoomReservations\DataTransferObjects\SaltRoomReservationSchedulesPdfResponse;
use Domain\SaltRoomReservations\DataTransferObjects\SaltRoomReservationSearchRequest;
use Domain\SaltRoomReservations\DataTransferObjects\SaltRoomReservationSearchResponse;
use Domain\SaltRoomReservations\DataTransferObjects\SaltRoomReservationSendUpcomingReservationEmailResponse;

interface SaltRoomReservationsService
{
    public function find(int $id, array $includes): ?SaltRoomReservationEntity;

    public function findByOrderDetail(int $id, array $includes): SaltRoomReservationSearchResponse;

    public function search(SaltRoomReservationSearchRequest $request): SaltRoomReservationSearchResponse;

    public function create(array $data): SaltRoomReservationEntity;

    public function sendUpcomingReservationEmail(int $id): SaltRoomReservationSendUpcomingReservationEmailResponse;

    public function markAsUsed(array $data): SaltRoomReservationEntity;

    public function update(array $data): SaltRoomReservationEntity;

    public function delete(array $data): SaltRoomReservationEntity;

    public function schedulesPdf(string $date): SaltRoomReservationSchedulesPdfResponse;

    public function sendEmail(array $data);
}
