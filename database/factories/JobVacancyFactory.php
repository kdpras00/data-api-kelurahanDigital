<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JobVacancy>
 */
class JobVacancyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $thumbnails = [
            'https://placehold.co/640x480/F59E0B/FFFFFF?text=Job+Vacancy',
            'https://placehold.co/640x480/D97706/FFFFFF?text=Lowongan',
            'https://placehold.co/640x480/B45309/FFFFFF?text=Pekerjaan',
        ];
        
        return [
            'thumbnail'         => $this->faker->randomElement($thumbnails),
            'job_title'         => $this->faker->jobTitle(),
            'description'       => $this->faker->paragraph(),
            'company_in_charge' => $this->faker->company(),
            'start_date'        => $this->faker->dateTimeBetween('-1 year', 'now'),
            'end_date'          => $this->faker->dateTimeBetween('now', '+1 year'),
            'salary'            => $this->faker->randomFloat(2, 100000, 1000000),
            'job_status'        => $this->faker->randomElement(['ongoing', 'completed']),
        ];
    }
}
