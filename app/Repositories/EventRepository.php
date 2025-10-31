<?php
namespace App\Repositories;

use App\Interfaces\EventRepositoryInterface;
use App\Models\Event;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class EventRepository implements EventRepositoryInterface
{
    public function getAll(
        ?string $search,
        ?int $limit,
        bool $execute,
        array $filters = []) {
        $query = Event::where(function ($query) use ($search) {
            // kondisi jika melakukan pencarian data yang didefinisikan dalam model user
            if ($search) {
                // melakukan search
                $query->search($search);
            }
        })->with('eventParticipants');

        // Filter berdasarkan is_active
        if (isset($filters['is_active']) && $filters['is_active'] !== null && $filters['is_active'] !== '') {
            $query->where('is_active', $filters['is_active']);
        }

        // Filter berdasarkan price range
        if (isset($filters['price_min']) && $filters['price_min']) {
            $query->where('price', '>=', $filters['price_min']);
        }
        if (isset($filters['price_max']) && $filters['price_max']) {
            $query->where('price', '<=', $filters['price_max']);
        }

        // Filter berdasarkan date range
        if (isset($filters['date_from']) && $filters['date_from']) {
            $query->where('date', '>=', $filters['date_from']);
        }
        if (isset($filters['date_to']) && $filters['date_to']) {
            $query->where('date', '<=', $filters['date_to']);
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
        $query = Event::where('id', $id)->with('eventParticipants');

        return $query->first();
    }

    public function create(array $data)
    {
        DB::beginTransaction();

        try {

            $event = new Event;
            
            // Store thumbnail
            $path = $data['thumbnail']->store('assets/events', 'public');
            $event->thumbnail = $path;
            
            // Copy file to public/storage for direct access (XAMPP doesn't follow symlinks well)
            $sourcePath = storage_path('app/public/' . $path);
            $destinationPath = public_path('storage/' . $path);
            
            // Ensure destination directory exists
            File::ensureDirectoryExists(dirname($destinationPath));
            
            // Copy file
            File::copy($sourcePath, $destinationPath);
            
            $event->name        = $data['name'];
            $event->description = $data['description'];
            $event->price       = $data['price'];
            $event->date        = $data['date'];
            $event->time        = $data['time'];

            if (isset($data['is_active'])) {
                $event->is_active = $data['is_active'];
            }

            $event->save();

            DB::commit();

            return $event;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    public function update(string $id, array $data)
    {
        DB::beginTransaction();

        try {
            $event = Event::find($id);

            if (isset($data['thumbnail'])) {
                // Delete old thumbnail if exists and is not a URL
                if ($event->thumbnail && 
                    !str_starts_with($event->thumbnail, 'http://') && 
                    !str_starts_with($event->thumbnail, 'https://')) {
                    Storage::disk('public')->delete($event->thumbnail);
                    $oldPublicPath = public_path('storage/' . $event->thumbnail);
                    if (File::exists($oldPublicPath)) {
                        File::delete($oldPublicPath);
                    }
                }
                
                // Store new thumbnail
                $path = $data['thumbnail']->store('assets/events', 'public');
                $event->thumbnail = $path;
                
                // Copy file to public/storage for direct access (XAMPP doesn't follow symlinks well)
                $sourcePath = storage_path('app/public/' . $path);
                $destinationPath = public_path('storage/' . $path);
                
                // Ensure destination directory exists
                File::ensureDirectoryExists(dirname($destinationPath));
                
                // Copy file
                File::copy($sourcePath, $destinationPath);
            }
            
            $event->name        = $data['name'];
            $event->description = $data['description'];
            $event->price       = $data['price'];
            $event->date        = $data['date'];
            $event->time        = $data['time'];

            if (isset($data['is_active'])) {
                $event->is_active = $data['is_active'];
            }

            $event->save();

            DB::commit();

            return $event;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    public function delete(string $id)
    {
        DB::beginTransaction();

        try {
            $event = Event::find($id);
            
            // Delete thumbnail if exists
            if ($event->thumbnail && 
                !str_starts_with($event->thumbnail, 'http://') && 
                !str_starts_with($event->thumbnail, 'https://')) {
                Storage::disk('public')->delete($event->thumbnail);
                $publicPath = public_path('storage/' . $event->thumbnail);
                if (File::exists($publicPath)) {
                    File::delete($publicPath);
                }
            }
            
            $event->delete();

            DB::commit();

            return $event;
        } catch (\Exception $e) {
            //throw $th;
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }
}
