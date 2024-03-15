<?php

namespace Domain\SaltRoomReservations\Providers;

use Domain\SaltRoomReservations\Contracts\Repositories\SaltRoomReservationOrderDetailsRepository as SaltRoomReservationOrderDetailsRepositoryInterface;
use Domain\SaltRoomReservations\Contracts\Repositories\SaltRoomReservationsRepository as SaltRoomReservationsRepositoryInterface;
use Domain\SaltRoomReservations\Contracts\Services\SaltRoomReservationsService as SaltRoomReservationsServiceInterface;
use Domain\SaltRoomReservations\Repositories\SaltRoomReservationsRepository;
use Domain\SaltRoomReservations\Repositories\SaltRoomReservationOrderDetailsRepository;
use Domain\SaltRoomReservations\Services\SaltRoomReservationsService;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class SaltRoomReservationsDomainServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array
    {
        return [
            // Repositories
            SaltRoomReservationsRepositoryInterface::class,
            SaltRoomReservationOrderDetailsRepositoryInterface::class,

            // Services
            SaltRoomReservationsServiceInterface::class,
        ];
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register(): void
    {
        // Repositories
        $this->app->bind(SaltRoomReservationsRepositoryInterface::class, SaltRoomReservationsRepository::class);
        $this->app->bind(SaltRoomReservationOrderDetailsRepositoryInterface::class, SaltRoomReservationOrderDetailsRepository::class);

        // Services
        $this->app->bind(SaltRoomReservationsServiceInterface::class, SaltRoomReservationsService::class);
    }
}
