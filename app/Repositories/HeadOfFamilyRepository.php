<?php
namespace App\Repositories;

use App\Interfaces\HeadOfFamilyRepositoryInterface;
use App\Models\HeadOfFamily;
use Exception;
use Illuminate\Support\Facades\DB;

class HeadOfFamilyRepository implements HeadOfFamilyRepositoryInterface
{
    public function getAll(
        ?string $search,
        ?int $limit,
        bool $execute,
        array $filters = []) {

        $query = HeadOfFamily::where(function ($query) use ($search) {
            // kondisi jika melakukan pencarian data yang didefinisikan dalam model user
            if ($search) {
                // melakukan search
                $query->search($search);
            }
        });

        // Filter berdasarkan gender
        if (isset($filters['gender']) && $filters['gender']) {
            $query->where('gender', $filters['gender']);
        }

        // Filter berdasarkan marital_status
        if (isset($filters['marital_status']) && $filters['marital_status']) {
            $query->where('marital_status', $filters['marital_status']);
        }

        // Filter berdasarkan occupation
        if (isset($filters['occupation']) && $filters['occupation']) {
            $query->where('occupation', 'like', '%' . $filters['occupation'] . '%');
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
        array $filters = []) {
        $query = $this->getAll($search, $rowPerPage, false, $filters);

        return $query->paginate($rowPerPage);
    }

    public function getById(string $id)
    {
        $query = HeadOfFamily::with([
            'user.jobApplicants.jobVacancy',
            'familyMembers.user',
            'socialAssistanceRecipients.socialAssistance',
            'eventParticipants.event',
        ])->where('id', $id);

        return $query->first();
    }

    public function create(
        array $data
    ) {
        DB::beginTransaction();

        try {
            // karena sebelum dijadikan akun head of family, harus terlebih dahulu dijadikan akun user

            // proses panggil data user repository
            $userRepository = new UserRepository();

            $user = $userRepository->create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => $data['password'],
            ])->assignRole('head-of-family');

            $headOfFamily                  = new HeadOfFamily();
            $headOfFamily->user_id         = $user->id;
            $headOfFamily->profile_picture = $data['profile_picture']->store('assets/head-of-families', 'public');
            $headOfFamily->identity_number = $data['identity_number'];
            $headOfFamily->gender          = $data['gender'];
            $headOfFamily->date_of_birth   = $data['date_of_birth'];
            $headOfFamily->phone_number    = $data['phone_number'];
            $headOfFamily->occupation      = $data['occupation'];
            $headOfFamily->marital_status  = $data['marital_status'];

            $headOfFamily->save();

            DB::commit();

            return $headOfFamily;
        } catch (\Exception $e) {
            //throw $th;
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    public function update(
        string $id,
        array $data
    ) {
        DB::beginTransaction();

        try {
            $headOfFamily = HeadOfFamily::find($id);

            if (isset($data['profile_picture'])) {
                $headOfFamily->profile_picture = $data['profile_picture']->store('assets/head-of-families', 'public');
            }
            $headOfFamily->identity_number = $data['identity_number'];
            $headOfFamily->gender          = $data['gender'];
            $headOfFamily->date_of_birth   = $data['date_of_birth'];
            $headOfFamily->phone_number    = $data['phone_number'];
            $headOfFamily->occupation      = $data['occupation'];
            $headOfFamily->marital_status  = $data['marital_status'];

            $headOfFamily->save();

            $userRepository = new UserRepository();

            $userRepository->update($headOfFamily->user_id, [
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => isset($data['password']) ? bcrypt($data['password']) : $headOfFamily->user->password,
            ]);
            DB::commit();

            return $headOfFamily;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    public function delete(string $id)
    {
        DB::beginTransaction();

        try {
            $headOfFamily = HeadOfFamily::find($id);
            $headOfFamily->delete();

            DB::commit();

            return $headOfFamily;
        } catch (\Exception $e) {
            //throw $th;
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }
}
