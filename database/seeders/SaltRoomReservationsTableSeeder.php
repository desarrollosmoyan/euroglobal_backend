<?php

namespace Database\Seeders;

use Domain\SaltRoomReservations\Models\SaltRoomReservation;
use Illuminate\Database\Seeder;

class SaltRoomReservationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        SaltRoomReservation::factory(50)->create();
    }
}
