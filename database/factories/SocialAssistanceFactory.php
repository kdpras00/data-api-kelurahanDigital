<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SocialAssistance>
 */
class SocialAssistanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Use local placeholder image or dummy path instead of external URL
        $thumbnails = [
            'https://placehold.co/640x480/10B981/FFFFFF?text=Bantuan+Sosial',
            'https://placehold.co/640x480/059669/FFFFFF?text=Bantuan+Pangan',
            'https://placehold.co/640x480/047857/FFFFFF?text=Bantuan+Tunai',
            'https://placehold.co/640x480/065F46/FFFFFF?text=Kesehatan',
        ];
        
        return [
            //
            'thumbnail'    => $this->faker->randomElement($thumbnails),
            'name'         => $this->faker->randomElement(['Bantuan Pangan', 'Bantuan tunai', 'Bansos Pendidikan', 'Bansos Kesehatan']) . ' ' . $this->faker->company,
            'category'     => $this->faker->randomElement(['staple', 'cash', 'subsidized fuel', 'health']),
            'amount'       => $this->faker->randomFloat(2, 1000, 10000),
            'provider'     => $this->faker->company,
            'description'  => $this->faker->sentence,
            'is_available' => $this->faker->boolean(),
        ];
    }
}
