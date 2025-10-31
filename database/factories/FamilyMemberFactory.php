<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FamilyMember>
 */
class FamilyMemberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Use reliable placeholder service
        $gender = $this->faker->randomElement(['male', 'female']);
        $profilePics = [
            'male' => 'https://placehold.co/400x400/3B82F6/FFFFFF?text=Male',
            'female' => 'https://placehold.co/400x400/EC4899/FFFFFF?text=Female',
        ];
        
        return [
            //
            'profile_picture' => $profilePics[$gender],
            'identity_number' => $this->faker->unique()->numberBetween(1000000, 9999999),
            'gender'          => $gender,
            'date_of_birth'   => $this->faker->dateTimeBetween('-60 years', 'now'),
            'phone_number'    => $this->faker->unique()->phoneNumber(),
            'occupation'      => $this->faker->jobTitle(),
            'marital_status'  => $this->faker->randomElement(['married', 'single']),
            'relation'        => $this->faker->randomElement(['husband', 'wife', 'child']),
        ];
    }
}
