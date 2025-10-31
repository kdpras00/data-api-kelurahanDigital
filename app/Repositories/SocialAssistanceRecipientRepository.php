<?php
namespace App\Repositories;

use App\Interfaces\SocialAssistanceRecipientRepositoryInterface;
use App\Models\SocialAssistanceRecipient;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class SocialAssistanceRecipientRepository implements SocialAssistanceRecipientRepositoryInterface
{
    public function getAll(
        ?string $search,
        ?int $limit,
        bool $execute,
        array $filters = []) {
        $query = SocialAssistanceRecipient::with(['headOfFamily.user', 'socialAssistance'])
            ->where(function ($query) use ($search) {
                // kondisi jika melakukan pencarian data yang didefinisikan dalam model user
                if ($search) {
                    // melakukan search
                    $query->search($search);
                }
            });

        // Filter berdasarkan status
        if (isset($filters['status']) && $filters['status']) {
            $query->where('status', $filters['status']);
        }

        // Filter berdasarkan bank
        if (isset($filters['bank']) && $filters['bank']) {
            $query->where('bank', 'like', '%' . $filters['bank'] . '%');
        }

        $query->orderBy('created_at', 'desc');

        $user = auth()->user();
        if ($user && $user->hasRole('head-of-family') && $user->headOfFamily) {
            $query->where('head_of_family_id', $user->headOfFamily->id);
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

    public function getAllPaginated(
        ?string $search,
        ?int $rowPerPage,
        array $filters = []) {
        $query = $this->getAll($search, null, false, $filters);

        return $query->paginate($rowPerPage);
    }

    public function getById(string $id)
    {
        $query = SocialAssistanceRecipient::where('id', $id);

        return $query->first();
    }
    public function create(array $data)
    {
        DB::beginTransaction();

        try {
            $socialAssistanceRecipient                       = new SocialAssistanceRecipient;
            $socialAssistanceRecipient->social_assistance_id = $data['social_assistance_id'];
            $socialAssistanceRecipient->head_of_family_id    = $data['head_of_family_id'];
            $socialAssistanceRecipient->amount               = $data['amount'];
            $socialAssistanceRecipient->reason               = $data['reason'];
            $socialAssistanceRecipient->bank                 = $data['bank'];
            $socialAssistanceRecipient->account_number       = $data['account_number'];

            if (isset($data['proof'])) {
                $path = $data['proof']->store(
                    'assets/social-assistance-recipients',
                    'public'
                );
                $socialAssistanceRecipient->proof = $path;
                
                // Copy file to public/storage for direct access (XAMPP doesn't follow symlinks well)
                $sourcePath = storage_path('app/public/' . $path);
                $destinationPath = public_path('storage/' . $path);
                
                // Ensure destination directory exists
                File::ensureDirectoryExists(dirname($destinationPath));
                
                // Copy file
                File::copy($sourcePath, $destinationPath);
            }
            if (isset($data['status'])) {
                $socialAssistanceRecipient->status = $data['status'];
            }
            $socialAssistanceRecipient->save();

            // Load relationships needed for notifications
            $socialAssistanceRecipient->load(['socialAssistance', 'headOfFamily.user']);

            DB::commit();

            return $socialAssistanceRecipient;
        } catch (\Exception $e) {
            DB::rollBack();

            throw new Exception($e->getMessage());
        }
    }

    public function update(string $id, array $data)
    {
        DB::beginTransaction();

        try {
            $socialAssistanceRecipient = SocialAssistanceRecipient::find($id);

            $socialAssistanceRecipient->social_assistance_id = $data['social_assistance_id'];
            $socialAssistanceRecipient->head_of_family_id    = $data['head_of_family_id'];
            $socialAssistanceRecipient->amount               = $data['amount'];
            $socialAssistanceRecipient->reason               = $data['reason'];
            $socialAssistanceRecipient->bank                 = $data['bank'];
            $socialAssistanceRecipient->account_number       = $data['account_number'];

            if (isset($data['proof'])) {
                // Delete old proof file if exists and is not a URL (from seeder)
                if ($socialAssistanceRecipient->proof && 
                    !str_starts_with($socialAssistanceRecipient->proof, 'http://') && 
                    !str_starts_with($socialAssistanceRecipient->proof, 'https://')) {
                    // Delete from both storage and public
                    Storage::disk('public')->delete($socialAssistanceRecipient->proof);
                    $oldPublicPath = public_path('storage/' . $socialAssistanceRecipient->proof);
                    if (File::exists($oldPublicPath)) {
                        File::delete($oldPublicPath);
                    }
                }
                
                // Store new proof file
                $path = $data['proof']->store(
                    'assets/social-assistance-recipients',
                    'public'
                );
                $socialAssistanceRecipient->proof = $path;
                
                // Copy file to public/storage for direct access (XAMPP doesn't follow symlinks well)
                $sourcePath = storage_path('app/public/' . $path);
                $destinationPath = public_path('storage/' . $path);
                
                // Ensure destination directory exists
                File::ensureDirectoryExists(dirname($destinationPath));
                
                // Copy file
                File::copy($sourcePath, $destinationPath);
            }
            if (isset($data['status'])) {
                $socialAssistanceRecipient->status = $data['status'];
            }
            $socialAssistanceRecipient->save();
            DB::commit();
            return $socialAssistanceRecipient;
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
            $socialAssistanceRecipient = SocialAssistanceRecipient::find($id);
            $socialAssistanceRecipient->delete();

            DB::commit();

            return $socialAssistanceRecipient;
        } catch (\Exception $e) {
            //throw $th;
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }
}
