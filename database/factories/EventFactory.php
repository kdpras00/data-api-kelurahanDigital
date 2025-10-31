<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $thumbnails = [
            'https://placehold.co/640x480/8B5CF6/FFFFFF?text=Event',
            'https://placehold.co/640x480/7C3AED/FFFFFF?text=Kerja+Bakti',
            'https://placehold.co/640x480/6D28D9/FFFFFF?text=Perayaan',
        ];
        
        return [
            'thumbnail'   => $this->faker->randomElement($thumbnails),
            'name'        => $this->faker->randomElement(['Kerja Bakti', 'Isra Miraj', 'Maulid Nabi', 'Hari Kemerdekaan']),
            'description' => $this->faker->sentence(),
            'price'       => $this->faker->randomFloat(2, 100000, 1000000),
            'date'        => $this->faker->dateTimeBetween('-1 year', 'now'),
            'time'        => $this->faker->time(),
            'is_active'   => $this->faker->boolean(),
        ];
    }
}
