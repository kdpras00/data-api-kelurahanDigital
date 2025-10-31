<?php
namespace Database\Seeders;

use Database\Factories\JobVacancyFactory;
use Illuminate\Database\Seeder;

class JobVacancySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        JobVacancyFactory::new ()->count(10)->create();
    }
}
