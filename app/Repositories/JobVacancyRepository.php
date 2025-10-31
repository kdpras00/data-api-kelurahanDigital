<?php
namespace App\Repositories;

use App\Interfaces\JobVacancyRepositoryInterface;
use App\Models\FamilyMember;
use App\Models\JobVacancy;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class JobVacancyRepository implements JobVacancyRepositoryInterface
{
    public function getAll(
        ?string $search,
        ?string $status,
        ?int $limit,
        bool $execute,
        array $filters = []) {
        $query = JobVacancy::where(function ($query) use ($search) {
            // kondisi jika melakukan pencarian data yang didefinisikan dalam model user
            if ($search) {
                // melakukan search
                $query->search($search);
            }
        })->with(['jobApplicants.user.headOfFamily']);

        if ($status === 'my-applications') {
            $query->whereHas('jobApplicants', function ($q) {
                $user = auth()->user();
                if ($user) {
                    $headOfFamily = $user->headOfFamily;
                    if ($headOfFamily) {
                        $members   = FamilyMember::where('head_of_family_id', $headOfFamily->id)->pluck('user_id')->toArray();
                        $members[] = $user->id;

                        $q->whereIn('user_id', $members);
                    }
                }
            });
        }

        // Filter berdasarkan job_status
        if (isset($filters['job_status']) && $filters['job_status']) {
            $query->where('job_status', $filters['job_status']);
        }

        // Filter berdasarkan company_in_charge
        if (isset($filters['company_in_charge']) && $filters['company_in_charge']) {
            $query->where('company_in_charge', 'like', '%' . $filters['company_in_charge'] . '%');
        }

        // Filter berdasarkan salary range
        if (isset($filters['salary_min']) && $filters['salary_min']) {
            $query->where('salary', '>=', $filters['salary_min']);
        }
        if (isset($filters['salary_max']) && $filters['salary_max']) {
            $query->where('salary', '<=', $filters['salary_max']);
        }

        $query->orderBy('created_at', 'desc');

        if ($limit) {
            // untuk membatasi data yang diambil berdasarkan limit
            $query->take($limit);
        }

        if ($execute) {
            // untuk menjalankan query
            return $query->get();
        }

        return $query;
    }

    public function getAllPaginated(
        ?string $search,
        ?string $status,
        ?int $rowPerPage,
        array $filters = []) {
        $query = $this->getAll($search, $status, null, false, $filters);

        return $query->paginate($rowPerPage);
    }

    public function getById(string $id)
    {
        $query = JobVacancy::where('id', $id)->with(['jobApplicants.user.headOfFamily']);

        return $query->first();
    }

    public function create(array $data)
    {
        DB::beginTransaction();

        try {

            $jobVacancy                    = new JobVacancy;
            
            // Store thumbnail
            $path = $data['thumbnail']->store('assets/job-vacancies', 'public');
            $jobVacancy->thumbnail = $path;
            
            // Copy file to public/storage for direct access (XAMPP doesn't follow symlinks well)
            $sourcePath = storage_path('app/public/' . $path);
            $destinationPath = public_path('storage/' . $path);
            
            // Ensure destination directory exists
            File::ensureDirectoryExists(dirname($destinationPath));
            
            // Copy file
            File::copy($sourcePath, $destinationPath);
            
            $jobVacancy->job_title         = $data['job_title'];
            $jobVacancy->description       = $data['description'];
            $jobVacancy->company_in_charge = $data['company_in_charge'];
            $jobVacancy->start_date        = $data['start_date'];
            $jobVacancy->end_date          = $data['end_date'];
            $jobVacancy->salary            = $data['salary'];
            $jobVacancy->job_status        = $data['job_status'];

            $jobVacancy->save();

            DB::commit();

            return $jobVacancy;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    public function update(
        string $id,
        array $data) {
        DB::beginTransaction();

        try {
            $jobVacancy = JobVacancy::find($id);

            if (isset($data['thumbnail'])) {
                // Delete old thumbnail if exists and is not a URL
                if ($jobVacancy->thumbnail && 
                    !str_starts_with($jobVacancy->thumbnail, 'http://') && 
                    !str_starts_with($jobVacancy->thumbnail, 'https://')) {
                    Storage::disk('public')->delete($jobVacancy->thumbnail);
                    $oldPublicPath = public_path('storage/' . $jobVacancy->thumbnail);
                    if (File::exists($oldPublicPath)) {
                        File::delete($oldPublicPath);
                    }
                }
                
                // Store new thumbnail
                $path = $data['thumbnail']->store('assets/job-vacancies', 'public');
                $jobVacancy->thumbnail = $path;
                
                // Copy file to public/storage for direct access (XAMPP doesn't follow symlinks well)
                $sourcePath = storage_path('app/public/' . $path);
                $destinationPath = public_path('storage/' . $path);
                
                // Ensure destination directory exists
                File::ensureDirectoryExists(dirname($destinationPath));
                
                // Copy file
                File::copy($sourcePath, $destinationPath);
            }

            $jobVacancy->job_title         = $data['job_title'];
            $jobVacancy->description       = $data['description'];
            $jobVacancy->company_in_charge = $data['company_in_charge'];
            $jobVacancy->start_date        = $data['start_date'];
            $jobVacancy->end_date          = $data['end_date'];
            $jobVacancy->salary            = $data['salary'];
            $jobVacancy->job_status        = $data['job_status'];

            $jobVacancy->save();

            DB::commit();

            return $jobVacancy;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    public function delete(string $id)
    {
        DB::beginTransaction();

        try {
            $jobVacancy = JobVacancy::find($id);
            $jobVacancy->delete();

            DB::commit();

            return $jobVacancy;
        } catch (\Exception $e) {
            //throw $th;
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }
}
