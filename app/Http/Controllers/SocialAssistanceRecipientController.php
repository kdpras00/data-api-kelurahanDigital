<?php
namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\SocialAssistanceRecipientStoreRequest;
use App\Http\Requests\SocialAssistanceRecipientUpdateRequest;
use App\Http\Resources\PaginateResource;
use App\Http\Resources\SocialAssistanceRecipientResource;
use App\Interfaces\SocialAssistanceRecipientRepositoryInterface;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Spatie\Permission\Middleware\PermissionMiddleware;

class SocialAssistanceRecipientController extends Controller implements HasMiddleware
{
    private SocialAssistanceRecipientRepositoryInterface $socialAssistanceRecipientRepository;
    private NotificationService $notificationService;

    public function __construct(
        SocialAssistanceRecipientRepositoryInterface $socialAssistanceRecipientRepository,
        NotificationService $notificationService
    ) {
        $this->socialAssistanceRecipientRepository = $socialAssistanceRecipientRepository;
        $this->notificationService = $notificationService;
    }

    public static function middleware()
    {
        return [
            new Middleware(PermissionMiddleware::using(['social-assistance-recipient-list|social-assistance-recipient-create|social-assistance-recipient-edit|social-assistance-recipient-delete']), only: ['index', 'getAllPaginated', 'show']),

            new Middleware(PermissionMiddleware::using(['social-assistance-recipient-create']), only: ['store']),

            new Middleware(PermissionMiddleware::using(['social-assistance-recipient-edit']), only: ['update']),

            new Middleware(PermissionMiddleware::using(['social-assistance-recipient-delete']), only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        //
        try {
            $socialAssistanceRecipients = $this->socialAssistanceRecipientRepository->getAll(
                $request->search,
                $request->limit,
                true
            );

            return ResponseHelper::jsonResponse(true, 'Data Penerima Bansos Berhasil Diambil', SocialAssistanceRecipientResource::collection($socialAssistanceRecipients), 200);
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
            'page'         => 'nullable|integer',
            'status'       => 'nullable|string|in:pending,approved,rejected',
            'bank'         => 'nullable|string',
        ]);

        try {
            // Pisahkan filter dari parameter lainnya
            $filters = [
                'status' => $request['status'] ?? null,
                'bank'   => $request['bank'] ?? null,
            ];

            $socialAssistanceRecipients = $this->socialAssistanceRecipientRepository->getAllPaginated(
                $request['search'] ?? null,
                $request['row_per_page'],
                $filters
            );

            return ResponseHelper::jsonResponse(true, 'Data Penerima Bansos Berhasil Diambil', PaginateResource::make($socialAssistanceRecipients, SocialAssistanceRecipientResource::class), 200);
        } catch (\Exception $e) {
            //throw $th;
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SocialAssistanceRecipientStoreRequest $request)
    {
        //
        $request = $request->validated();

        try {
            //code...
            $socialAssistanceRecipient = $this->socialAssistanceRecipientRepository->create($request);

            // Send notifications
            try {
                $this->notificationService->socialAssistanceRecipientCreated(
                    $socialAssistanceRecipient->socialAssistance,
                    $socialAssistanceRecipient->headOfFamily
                );
            } catch (\Exception $notifError) {
                // Log notification error but don't fail the whole request
                \Log::error('Failed to send notification for social assistance recipient: ' . $notifError->getMessage());
            }

            return ResponseHelper::jsonResponse(true, 'Data Penerima Bansos berhasil ditambahkan', new SocialAssistanceRecipientResource($socialAssistanceRecipient), 201);
        } catch (\Exception $e) {
            //throw $th;
            \Log::error('Error creating social assistance recipient: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
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
            $socialAssistanceRecipient = $this->socialAssistanceRecipientRepository->getById($id);

            if (! $socialAssistanceRecipient) {
                return ResponseHelper::jsonResponse(false, 'Data Penerima Bansos tidak ditemukan', null, 404);
            }

            return ResponseHelper::jsonResponse(true, 'Data Penerima Bansos berhasil diambil', new SocialAssistanceRecipientResource($socialAssistanceRecipient), 200);

        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SocialAssistanceRecipientUpdateRequest $request, string $id)
    {
        //

        $request = $request->validated();

        try {

            $socialAssistanceRecipient = $this->socialAssistanceRecipientRepository->getById($id);

            if (! $socialAssistanceRecipient) {
                return ResponseHelper::jsonResponse(false, 'Data Penerima Bansos tidak ditemukan', null, 404);
            }
            $socialAssistanceRecipient = $this->socialAssistanceRecipientRepository->update(
                $id,
                $request);

            // Send notification if status changed
            if (isset($request['status'])) {
                $this->notificationService->socialAssistanceRecipientStatusChanged(
                    $socialAssistanceRecipient->socialAssistance,
                    $socialAssistanceRecipient->headOfFamily,
                    $request['status']
                );
            }

            return ResponseHelper::jsonResponse(true, 'Data Penerima Bansos berhasil diupdate', new SocialAssistanceRecipientResource($socialAssistanceRecipient), 201);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $socialAssistanceRecipient = $this->socialAssistanceRecipientRepository->getById($id);

            if (! $socialAssistanceRecipient) {
                return ResponseHelper::jsonResponse(false, 'Data Penerima bansos tidak ditemukan', null, 404);
            }

            $socialAssistanceRecipient = $this->socialAssistanceRecipientRepository->delete($id);

            return ResponseHelper::jsonResponse(true, 'Data Penerima bansos berhasil dihapus', null, 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }
}
