<?php
namespace App\Repositories;

use App\Interfaces\JobApplicantRepositoryInterface;
use App\Models\JobApplicant;
use Exception;
use Illuminate\Support\Facades\DB;

class JobApplicantRepository implements JobApplicantRepositoryInterface
{
    public function getAll(
        ?string $search,
        ?int $limit,
        bool $execute,
        ?string $userId = null) {
        $query = JobApplicant::where(function ($query) use ($search) {
            // kondisi jika melakukan pencarian data yang didefinisikan dalam model user
            if ($search) {
                // melakukan search
                $query->search($search);
            }
        })->with(['jobVacancy', 'user']);

        // Filter by user_id if provided
        if ($userId) {
            $query->where('user_id', $userId);
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
        ?int $rowPerPage,
        ?string $userId = null) {
        $query = $this->getAll($search, $rowPerPage, false, $userId);

        return $query->paginate($rowPerPage);
    }

    public function getById(string $id)
    {
        $query = JobApplicant::where('id', $id);

        return $query->first();
    }

    public function create(array $data)
    {
        DB::beginTransaction();

        try {
            // Check if application already exists
            $existing = JobApplicant::where('job_vacancy_id', $data['job_vacancy_id'])
                ->where('user_id', $data['user_id'])
                ->first();

            if ($existing) {
                throw new Exception('Anda sudah melamar untuk lowongan pekerjaan ini.');
            }

            $jobApplicant                 = new JobApplicant;
            $jobApplicant->job_vacancy_id = $data['job_vacancy_id'];
            $jobApplicant->user_id        = $data['user_id'];

            if (isset($data['status'])) {
                $jobApplicant->status = $data['status'];
            }
            $jobApplicant->save();

            DB::commit();

            return $jobApplicant;
        } catch (\Exception $e) {
            DB::rollBack();

            throw new Exception($e->getMessage());
        }
    }

    public function update(string $id, array $data)
    {
        DB::beginTransaction();

        try {
            $jobApplicant = JobApplicant::find($id);

            if (isset($data['job_vacancy_id'])) {
                $jobApplicant->job_vacancy_id = $data['job_vacancy_id'];
            }
            
            if (isset($data['user_id'])) {
                $jobApplicant->user_id = $data['user_id'];
            }

            if (isset($data['status'])) {
                $jobApplicant->status = $data['status'];
            }
            
            $jobApplicant->save();

            DB::commit();

            return $jobApplicant;
        } catch (\Exception $e) {
            //throw $th;
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    public function delete(string $id)
    {
        DB::beginTransaction();

        try {
            $jobApplicant = JobApplicant::find($id);
            $jobApplicant->delete();

            DB::commit();

            return $jobApplicant;
        } catch (\Exception $e) {
            //throw $th;
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }
}
