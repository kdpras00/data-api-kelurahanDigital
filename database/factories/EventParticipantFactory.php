<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EventParticipant>
 */
class EventParticipantFactory extends Factory
{
    public function definition(): array
    {
        return [
            'quantity'       => $this->faker->numberBetween(1, 10),
            'total_price'    => $this->faker->numberBetween(10000, 100000),
            'payment_status' => $this->faker->randomElement(['pending', 'approved', 'rejected']),
        ];
    }
}
