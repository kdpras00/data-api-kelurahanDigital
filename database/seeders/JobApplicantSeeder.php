<?php
namespace Database\Seeders;

use App\Models\JobApplicant;
use App\Models\JobVacancy;
use App\Models\User;
use Illuminate\Database\Seeder;

class JobApplicantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get head of family user
        $headOfFamilyUser = User::where('email', 'headoffamily@gmail.com')->first();
        
        if ($headOfFamilyUser) {
            // Get first 3 job vacancies for the head of family user
            $jobVacancies = JobVacancy::take(3)->get();
            
            foreach ($jobVacancies as $jobVacancy) {
                JobApplicant::factory()->create([
                    'job_vacancy_id' => $jobVacancy->id,
                    'user_id'        => $headOfFamilyUser->id,
                ]);
            }
        }
        
        // Create random job applicants for other users
        $jobVacancies = JobVacancy::all();
        $users        = User::where('email', '!=', 'headoffamily@gmail.com')->take(3)->get();
        
        foreach ($jobVacancies->take(2) as $jobVacancy) {
            foreach ($users as $user) {
                JobApplicant::factory()->create([
                    'job_vacancy_id' => $jobVacancy->id,
                    'user_id'        => $user->id,
                ]);
            }
        }
    }
}
