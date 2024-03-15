<?php

namespace Database\Factories;

use Domain\Gyms\Models\GymSubscriptionNote;
use Domain\Gyms\Models\GymSubscription;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GymSubscriptionNote>
 */
class GymSubscriptionNoteFactory extends Factory
{
    protected $model = GymSubscriptionNote::class;

    /**
     * @return array
     */
    public function definition(): array
    {
        $subscription = GymSubscription::all()->random();
        $note = $this->faker->realText();

        return [
            'gym_subscription_id' => $subscription->id,
            'note' => $note,
        ];
    }
}
