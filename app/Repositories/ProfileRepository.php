<?php
namespace App\Repositories;

use App\Interfaces\ProfileRepositoryInterface;
use App\Models\Profile;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ProfileRepository implements ProfileRepositoryInterface
{
    // 'thumbnail',
    //       'name',
    //       'about',
    //       'headman',
    //       'people',
    //       'agricultural_area',
    //       'total_area',

    public function get()
    {
        return Profile::first();
    }
    public function create(array $data)
    {
        DB::beginTransaction();

        try {
            $profile = new Profile;
            
            // Store thumbnail
            $path = $data['thumbnail']->store('assets/profiles', 'public');
            $profile->thumbnail = $path;
            
            // Copy file to public/storage for direct access (XAMPP doesn't follow symlinks well)
            $sourcePath = storage_path('app/public/' . $path);
            $destinationPath = public_path('storage/' . $path);
            File::ensureDirectoryExists(dirname($destinationPath));
            File::copy($sourcePath, $destinationPath);
            
            $profile->name              = $data['name'];
            $profile->about             = $data['about'];
            $profile->headman           = $data['headman'];
            $profile->people            = $data['people'];
            $profile->agricultural_area = $data['agricultural_area'];
            $profile->total_area        = $data['total_area'];
            $profile->latitude          = $data['latitude'] ?? null;
            $profile->longitude         = $data['longitude'] ?? null;

            $profile->save();

            if (array_key_exists('images', $data) && !empty($data['images'])) {
                foreach ($data['images'] as $image) {
                    $imagePath = $image->store('assets/profiles', 'public');
                    
                    // Copy image to public/storage
                    $imageSource = storage_path('app/public/' . $imagePath);
                    $imageDestination = public_path('storage/' . $imagePath);
                    File::ensureDirectoryExists(dirname($imageDestination));
                    File::copy($imageSource, $imageDestination);
                    
                    $profile->profileImages()->create(['image' => $imagePath]);
                }
            }

            DB::commit();

            return $profile->fresh(['profileImages']);
        } catch (\Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    public function update(array $data)
    {
        DB::beginTransaction();

        try {
            $profile = Profile::first();

            if (!$profile) {
                throw new Exception('Profile tidak ditemukan');
            }

            if (isset($data['thumbnail'])) {
                // Delete old thumbnail from both storage locations
                if ($profile->thumbnail) {
                    $oldStoragePath = storage_path('app/public/' . $profile->thumbnail);
                    $oldPublicPath = public_path('storage/' . $profile->thumbnail);
                    
                    if (File::exists($oldStoragePath)) {
                        File::delete($oldStoragePath);
                    }
                    
                    if (File::exists($oldPublicPath)) {
                        File::delete($oldPublicPath);
                    }
                }
                
                // Store new thumbnail
                $path = $data['thumbnail']->store('assets/profiles', 'public');
                $profile->thumbnail = $path;
                
                // Copy file to public/storage
                $sourcePath = storage_path('app/public/' . $path);
                $destinationPath = public_path('storage/' . $path);
                File::ensureDirectoryExists(dirname($destinationPath));
                File::copy($sourcePath, $destinationPath);
            }
            
            $profile->name              = $data['name'];
            $profile->about             = $data['about'];
            $profile->headman           = $data['headman'];
            $profile->people            = $data['people'];
            $profile->agricultural_area = $data['agricultural_area'];
            $profile->total_area        = $data['total_area'];
            $profile->latitude          = $data['latitude'] ?? null;
            $profile->longitude         = $data['longitude'] ?? null;

            if (array_key_exists('images', $data) && !empty($data['images'])) {
                foreach ($data['images'] as $image) {
                    $imagePath = $image->store('assets/profiles', 'public');
                    
                    // Copy image to public/storage
                    $imageSource = storage_path('app/public/' . $imagePath);
                    $imageDestination = public_path('storage/' . $imagePath);
                    File::ensureDirectoryExists(dirname($imageDestination));
                    File::copy($imageSource, $imageDestination);
                    
                    $profile->profileImages()->create(['image' => $imagePath]);
                }
            }

            $profile->save();

            DB::commit();

            return $profile->fresh(['profileImages']);
        } catch (\Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    public function delete()
    {
        DB::beginTransaction();

        try {
            $profile = Profile::first();

            if (!$profile) {
                throw new Exception('Profile tidak ditemukan');
            }

            // Delete thumbnail from both storage locations
            if ($profile->thumbnail) {
                $storagePath = storage_path('app/public/' . $profile->thumbnail);
                $publicPath = public_path('storage/' . $profile->thumbnail);
                
                if (File::exists($storagePath)) {
                    File::delete($storagePath);
                }
                
                if (File::exists($publicPath)) {
                    File::delete($publicPath);
                }
            }

            // Delete profile images from both storage locations
            if ($profile->profileImages && $profile->profileImages->count() > 0) {
                foreach ($profile->profileImages as $profileImage) {
                    $storagePath = storage_path('app/public/' . $profileImage->image);
                    $publicPath = public_path('storage/' . $profileImage->image);
                    
                    if (File::exists($storagePath)) {
                        File::delete($storagePath);
                    }
                    
                    if (File::exists($publicPath)) {
                        File::delete($publicPath);
                    }
                    
                    $profileImage->delete();
                }
            }

            $profile->delete();

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }
}
