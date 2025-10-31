<?php
namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\SocialAssistanceStoreRequest;
use App\Http\Requests\SocialAssistanceUpdateRequest;
use App\Http\Resources\PaginateResource;
use App\Http\Resources\SocialAssistanceResource;
use App\Interfaces\SocialAssistanceRepositoryInterface;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Spatie\Permission\Middleware\PermissionMiddleware;

class SocialAssistanceController extends Controller implements HasMiddleware
{

    private SocialAssistanceRepositoryInterface $socialAssistanceRepository;
    private NotificationService $notificationService;

    public function __construct(
        SocialAssistanceRepositoryInterface $socialAssistanceRepository,
        NotificationService $notificationService
    ) {
        $this->socialAssistanceRepository = $socialAssistanceRepository;
        $this->notificationService = $notificationService;
    }

    public static function middleware()
    {
        return [
            new Middleware(PermissionMiddleware::using(['social-assistance-list|social-assistance-create|social-assistance-edit|social-assistance-delete']), only: ['index', 'getAllPaginated', 'show']),

            new Middleware(PermissionMiddleware::using(['social-assistance-create']), only: ['store']),

            new Middleware(PermissionMiddleware::using(['social-assistance-edit']), only: ['update']),

            new Middleware(PermissionMiddleware::using(['social-assistance-delete']), only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        //
        try {
            $SocialAssistances = $this->socialAssistanceRepository->getAll(
                $request->search,
                $request->limit,
                true
            );

            return ResponseHelper::jsonResponse(true, 'Data Bansos Berhasil Diambil', SocialAssistanceResource::collection($SocialAssistances), 200);
        } catch (\Exception $e) {
            //throw $th;
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    public function getAllPaginated(Request $request)
    {
        //
        $request = $request->validate([
            'search'       => 'nullable|string',
            'row_per_page' => 'required|integer',
            'category'     => 'nullable|string',
            'provider'     => 'nullable|string',
            'is_available' => 'nullable|boolean',
        ]);

        try {
            // Pisahkan filter dari parameter lainnya
            $filters = [
                'category'     => $request['category'] ?? null,
                'provider'     => $request['provider'] ?? null,
                'is_available' => $request['is_available'] ?? null,
            ];

            $socialAssistances = $this->socialAssistanceRepository->getAllPaginated(
                $request['search'] ?? null,
                $request['row_per_page'],
                $filters
            );

            return ResponseHelper::jsonResponse(true, 'Data Bansos Berhasil Diambil', PaginateResource::make($socialAssistances, SocialAssistanceResource::class), 200);
        } catch (\Exception $e) {
            //throw $th;
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(SocialAssistanceStoreRequest $request)
    {
        //
        $request = $request->validated();

        try {
            $socialAssistance = $this->socialAssistanceRepository->create($request);

            // Notify all head-of-family about new social assistance
            $this->notificationService->socialAssistanceCreated($socialAssistance);

            return ResponseHelper::jsonResponse(true, 'Data Bansos berhasil ditambahkan', new SocialAssistanceResource($socialAssistance), 201);
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
            $socialAssistance = $this->socialAssistanceRepository->getById($id);

            if (! $socialAssistance) {
                return ResponseHelper::jsonResponse(false, 'Data Bansos tidak ditemukan', null, 404);
            }

            return ResponseHelper::jsonResponse(true, 'Data Bansos berhasil diambil', new SocialAssistanceResource($socialAssistance), 200);

        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

/**
 * Update the specified resource in storage.
 */
    public function update(SocialAssistanceUpdateRequest $request, string $id)
    {
        //
        $request = $request->validated();

        try {

            $socialAssistance = $this->socialAssistanceRepository->getById($id);

            if (! $socialAssistance) {
                return ResponseHelper::jsonResponse(false, 'Data Bansos tidak ditemukan', null, 404);
            }
            $socialAssistance = $this->socialAssistanceRepository->update(
                $id,
                $request);

            return ResponseHelper::jsonResponse(true, 'Data Bansos berhasil diupdate', new SocialAssistanceResource($socialAssistance), 201);
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
            $socialAssistance = $this->socialAssistanceRepository->getById($id);

            if (! $socialAssistance) {
                return ResponseHelper::jsonResponse(false, 'Data bansos tidak ditemukan', null, 404);
            }

            $socialAssistance = $this->socialAssistanceRepository->delete($id);

            return ResponseHelper::jsonResponse(true, 'Data bansos berhasil dihapus', null, 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }
}
