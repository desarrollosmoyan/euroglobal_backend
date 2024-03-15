<?php

namespace Database\Seeders;

use Domain\Gyms\Models\GymSubscriptionNote;
use Illuminate\Database\Seeder;

class GymSubscriptionNotesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        GymSubscriptionNote::factory(100)->create();
    }
}
