<?php
namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\ProfileStoreRequest;
use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Resources\ProfileResource;
use App\Interfaces\ProfileRepositoryInterface;

class ProfileController extends Controller
{
    private ProfileRepositoryInterface $profileRepository;

    public function __construct(ProfileRepositoryInterface $profileRepository)
    {
        $this->profileRepository = $profileRepository;
    }

    public function index()
    {
        try {
            $profile = $this->profileRepository->get();

            if (! $profile) {
                return ResponseHelper::jsonResponse(false, 'Data Profile tidak ditemukan', null, 200);
            }

            return ResponseHelper::jsonResponse(true, 'Data Profile berhasil diambil', new ProfileResource($profile), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }
    public function store(ProfileStoreRequest $Request)
    {
        $Request = $Request->validated();
        try {
            $profile = $this->profileRepository->create($Request);

            return ResponseHelper::jsonResponse(true, 'Data Profile Berhasil Ditambahkan', new ProfileResource($profile), 201);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    public function update(ProfileUpdateRequest $Request)
    {
        $Request = $Request->validated();
        try {
            $profile = $this->profileRepository->update($Request);

            return ResponseHelper::jsonResponse(true, 'Data Profile Berhasil Diubah', new ProfileResource($profile), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    public function destroy()
    {
        try {
            $this->profileRepository->delete();

            return ResponseHelper::jsonResponse(true, 'Data Profile Berhasil Dihapus', null, 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }
}
