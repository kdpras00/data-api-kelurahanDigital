<?php
namespace App\Repositories;

use App\Interfaces\SocialAssistanceRepositoryInterface;
use App\Models\SocialAssistance;
use Exception;
use Illuminate\Support\Facades\DB;

class SocialAssistanceRepository implements SocialAssistanceRepositoryInterface
{

    public function getAll(
        ?string $search,
        ?int $limit,
        bool $execute,
        array $filters = []) {
        $query = SocialAssistance::where(function ($query) use ($search) {
            // kondisi jika melakukan pencarian data yang didefinisikan dalam model user
            if ($search) {
                // melakukan search
                $query->search($search);
            }
        })
        // Gunakan withCount untuk performa lebih baik, hanya hitung jumlah recipients
        ->withCount('socialAssistanceRecipients');

        // Filter berdasarkan category
        if (isset($filters['category']) && $filters['category']) {
            $query->where('category', $filters['category']);
        }

        // Filter berdasarkan provider
        if (isset($filters['provider']) && $filters['provider']) {
            $query->where('provider', 'like', '%' . $filters['provider'] . '%');
        }

        // Filter berdasarkan is_available
        if (isset($filters['is_available']) && $filters['is_available'] !== null && $filters['is_available'] !== '') {
            $query->where('is_available', $filters['is_available']);
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
        // Untuk detail view, kita tetap butuh relasi lengkap
        $query = SocialAssistance::where('id', $id)
            ->with('socialAssistanceRecipients');

        return $query->first();
    }

    public function create(array $data)
    {
        DB::beginTransaction();

        try {
            $socialAssistance               = new SocialAssistance;
            $socialAssistance->thumbnail    = $data['thumbnail']->store('assets/social-assistances', 'public');
            $socialAssistance->name         = $data['name'];
            $socialAssistance->category     = $data['category'];
            $socialAssistance->amount       = $data['amount'];
            $socialAssistance->provider     = $data['provider'];
            $socialAssistance->description  = $data['description'];
            $socialAssistance->is_available = $data['is_available'];

            $socialAssistance->save();

            DB::commit();

            return $socialAssistance;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    public function update(string $id, array $data)
    {
        DB::beginTransaction();

        try {
            $socialAssistance = SocialAssistance::find($id);

            if (isset($data['thumbnail'])) {
                $socialAssistance->thumbnail = $data['thumbnail']->store('assets/social-assistances', 'public');
            }
            $socialAssistance->name         = $data['name'];
            $socialAssistance->category     = $data['category'];
            $socialAssistance->amount       = $data['amount'];
            $socialAssistance->provider     = $data['provider'];
            $socialAssistance->description  = $data['description'];
            $socialAssistance->is_available = $data['is_available'];

            $socialAssistance->save();

            DB::commit();

            return $socialAssistance;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    public function delete(string $id)
    {
        DB::beginTransaction();

        try {
            $socialAssistance = SocialAssistance::find($id);
            $socialAssistance->delete();

            DB::commit();

            return $socialAssistance;
        } catch (\Exception $e) {
            //throw $th;
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }
}
