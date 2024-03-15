<?php

namespace Database\Factories;

use Illuminate\Support\Carbon;
use Domain\Orders\Models\Order;
use Domain\Clients\Models\Client;
use Illuminate\Support\Facades\DB;
use Domain\Orders\Models\OrderDetail;
use Illuminate\Database\Eloquent\Factories\Factory;
use Domain\SaltRoomReservations\Models\SaltRoomReservation;

/**
 * @extends Factory<SaltRoomReservation>
 */
class SaltRoomReservationFactory extends Factory
{
    protected $model = SaltRoomReservation::class;

    /**
     * @return array
     */
    public function definition(): array
    {
        $reservationsDaysAfterNow = $this->faker->randomDigitNotNull();
        $hourRanges = [
            [
                '09:30',
                '180',
            ],
            [
                '10:00',
                '180',
            ],
            [
                '10:30',
                '180',
            ],
            [
                '11:00',
                '180',
            ],
            [
                '12:00',
                '180',
            ],
        ];
        $selectedRange = $hourRanges[$this->faker->numberBetween(0, count($hourRanges) - 1)];
        $date = Carbon::now()->addDays($reservationsDaysAfterNow);
        $client = Client::paginate(100)->random();

        return [
            'client_id' => $client->id,
            'date' => $date,
            'time' => $selectedRange[0],
            'duration' => $selectedRange[1],
            'adults' => $this->faker->randomElement([
                '1',
                '2',
            ]),
            'children' => $this->faker->randomElement([
                '1',
                '2',
            ]),
            'used' => $this->faker->boolean(),
            'created_by' => 1,
            'last_modified_by' => 1,
        ];
    }

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterMaking(function (SaltRoomReservation $saltRoomReservation) {
            //
        })->afterCreating(function (SaltRoomReservation $saltRoomReservation) {
            $order = Order::create(
                [
                    'client_id' => $saltRoomReservation->client_id,
                    'locator' => uniqid('', false),
                    'source' => 'CRM',
                    'total_price' => '37',
                    'company_id' => 1,
                    'discount' => 'prueba'
                ]
            );

            $orderDetail = OrderDetail::create([
                'order_id' => $order->id,
                'product_id' => 2,
                'product_name' => 'ENTRADA GENERAL',
                'quantity' => 1,
                'price' => 37,
                'circuit_sessions' => 1,
                'treatment_sessions' => 1,
            ]);

            DB::unprepared("
                INSERT INTO salt_room_reservations_order_details (id, order_detail_id) VALUES ($saltRoomReservation->id, $orderDetail->id)
            ");
        });
    }
}
