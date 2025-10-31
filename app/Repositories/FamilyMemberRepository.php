<?php
namespace App\Repositories;

use App\Interfaces\FamilyMemberRepositoryInterface;
use App\Models\FamilyMember;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Support\Facades\DB;

class FamilyMemberRepository implements FamilyMemberRepositoryInterface
{
    public function getAll(
        ?string $search,
        ?int $limit,
        bool $execute,
        ?string $relation = null) {

        $query = FamilyMember::where(function ($query) use ($search) {
            // kondisi jika melakukan pencarian data yang didefinisikan dalam model user
            if ($search) {
                // melakukan search
                $query->search($search);
            }
        });

        $user = auth()->user();
        if ($user->hasRole('head-of-family') && $user->headOfFamily) {
            $query->where('head_of_family_id', $user->headOfFamily->id);
        }

        // Filter by relation if provided
        if ($relation && $relation !== 'all') {
            $query->where('relation', $relation);
        }

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

    public function getAllPaginated(?string $search, ?int $rowPerPage, ?string $relation = null)
    {
        $query = $this->getAll($search, null, false, $relation);

        return $query->paginate($rowPerPage);
    }

    public function getById(string $id)
    {
        $query = FamilyMember::where('id', $id)->with('headOfFamily');

        return $query->first();
    }

    public function create(array $data)
    {
        DB::beginTransaction();

        try {
            // karena sebelum dijadikan akun head of family, harus terlebih dahulu dijadikan akun user

            // proses panggil data user repository
            $userRepository = new UserRepository();

            $user = $userRepository->create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => $data['password'],
            ]);

            $familyMember                    = new FamilyMember;
            $familyMember->user_id           = $user->id;
            $familyMember->head_of_family_id = $data['head_of_family_id'];
            $familyMember->profile_picture   = $data['profile_picture']->store('assets/family-members', 'public');
            $familyMember->identity_number   = $data['identity_number'];
            $familyMember->gender            = $data['gender'];
            $familyMember->date_of_birth     = $data['date_of_birth'];
            $familyMember->phone_number      = $data['phone_number'];
            $familyMember->occupation        = $data['occupation'];
            $familyMember->marital_status    = $data['marital_status'];
            $familyMember->relation          = $data['relation'];
            $familyMember->save();

            DB::commit();

            return $familyMember;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    public function update(string $id, array $data)
    {
        DB::beginTransaction();

        try {
            $familyMember = FamilyMember::find($id);

            if (isset($data['profile_picture'])) {
                $familyMember->profile_picture = $data['profile_picture']->store('assets/family-members', 'public');
            }
            $familyMember->identity_number = $data['identity_number'];
            $familyMember->gender          = $data['gender'];
            $familyMember->date_of_birth   = $data['date_of_birth'];
            $familyMember->phone_number    = $data['phone_number'];
            $familyMember->occupation      = $data['occupation'];
            $familyMember->marital_status  = $data['marital_status'];
            $familyMember->relation        = $data['relation'];
            $familyMember->save();

            $userRepository = new UserRepository();

            $userRepository->update($familyMember->user_id, [
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => isset($data['password']) ? bcrypt($data['password']) : $familyMember->user->password,
            ]);
            DB::commit();

            return $familyMember;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    public function delete(string $id)
    {
        DB::beginTransaction();

        try {
            $familyMember = FamilyMember::find($id);
            $familyMember->delete();

            DB::commit();

            return $familyMember;
        } catch (\Exception $e) {
            //throw $th;
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }
}
