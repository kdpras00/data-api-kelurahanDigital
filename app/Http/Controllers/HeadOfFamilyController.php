<?php
namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\HeadOfFamilyStoreRequest;
use App\Http\Requests\HeadOfFamilyUpdateRequest;
use App\Http\Resources\HeadOfFamilyResource;
use App\Http\Resources\PaginateResource;
use App\Interfaces\HeadOfFamilyRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Spatie\Permission\Middleware\PermissionMiddleware;

class HeadOfFamilyController extends Controller implements HasMiddleware
{

    private HeadOfFamilyRepositoryInterface $headOfFamilyRepository;

    public function __construct(HeadOfFamilyRepositoryInterface $headOfFamilyRepository)
    {
        $this->headOfFamilyRepository = $headOfFamilyRepository;
    }

    public static function middleware()
    {
        return [
            new Middleware(PermissionMiddleware::using(['head-of-family-list|head-of-family-create|head-of-family-edit|head-of-family-delete']), only: ['index', 'getAllPaginated', 'show']),

            new Middleware(PermissionMiddleware::using(['head-of-family-create']), only: ['store']),

            new Middleware(PermissionMiddleware::using(['head-of-family-edit']), only: ['update']),

            new Middleware(PermissionMiddleware::using(['head-of-family-delete']), only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        try {
            $headOfFamilies = $this->headOfFamilyRepository->getAll(
                $request->search,
                $request->limit,
                true
            );

            return ResponseHelper::jsonResponse(true, 'Data Kepala Keluarga Berhasil Diambil', HeadOfFamilyResource::collection($headOfFamilies), 200);
        } catch (\Exception $e) {
            //throw $th;
            return ResponseHelper::jsonResponse(false, 'Data Kepala Keluarga gagal diambil', null, 500);
        }
    }

    public function getAllPaginated(Request $request)
    {
        //
        $request = $request->validate([
            'search'         => 'nullable|string',
            'row_per_page'   => 'required|integer',
            'gender'         => 'nullable|string|in:male,female',
            'marital_status' => 'nullable|string|in:single,married,divorced,widowed',
            'occupation'     => 'nullable|string',
        ]);

        try {
            // Pisahkan filter dari parameter lainnya
            $filters = [
                'gender'         => $request['gender'] ?? null,
                'marital_status' => $request['marital_status'] ?? null,
                'occupation'     => $request['occupation'] ?? null,
            ];

            $headOfFamilies = $this->headOfFamilyRepository->getAllPaginated(
                $request['search'] ?? null,
                $request['row_per_page'],
                $filters
            );

            return ResponseHelper::jsonResponse(true, 'Data Kepala Keluarga Berhasil Diambil', PaginateResource::make($headOfFamilies, HeadOfFamilyResource::class), 200);
        } catch (\Exception $e) {
            //throw $th;
            return ResponseHelper::jsonResponse(false, 'Data Kepala Keluarga gagal diambil', null, 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(HeadOfFamilyStoreRequest $request)
    {
        //
        $request = $request->validated();

        try {
            $headOfFamily = $this->headOfFamilyRepository->create($request);

            return ResponseHelper::jsonResponse(true, 'Kepala Keluarga berhasil ditambahkan', new HeadOfFamilyResource($headOfFamily), 201);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        try {
            $headOfFamily = $this->headOfFamilyRepository->getById($id);

            if (! $headOfFamily) {
                return ResponseHelper::jsonResponse(false, 'Data Kepala Keluarga tidak ditemukan', null, 404);
            }

            return ResponseHelper::jsonResponse(true, 'Data Kepala Keluarga berhasil diambil', new HeadOfFamilyResource($headOfFamily), 200);

        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(HeadOfFamilyUpdateRequest $request, string $id)
    {
        //
        $request = $request->validated();

        try {
            $headOfFamily = $this->headOfFamilyRepository->getById($id);

            if (! $headOfFamily) {
                return ResponseHelper::jsonResponse(false, 'Data Kepala Keluarga tidak ditemukan', null, 404);
            }

            $headOfFamily = $this->headOfFamilyRepository->update($id, $request);

            return ResponseHelper::jsonResponse(true, 'Kepala Keluarga berhasil diupdate', new HeadOfFamilyResource($headOfFamily), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        try {
            $headOfFamily = $this->headOfFamilyRepository->getById($id);

            if (! $headOfFamily) {
                return ResponseHelper::jsonResponse(false, 'Data Kepala Keluarga tidak ditemukan', null, 404);
            }

            $headOfFamily = $this->headOfFamilyRepository->delete($id);

            return ResponseHelper::jsonResponse(true, 'Kepala Keluarga berhasil dihapus', null, 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }
}
