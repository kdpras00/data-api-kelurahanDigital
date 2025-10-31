<?php
namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\EventParticipantStoreRequest;
use App\Http\Requests\EventParticipantUpdateRequest;
use App\Http\Resources\EventParticipantResource;
use App\Http\Resources\PaginateResource;
use App\Interfaces\EventParticipantRepositoryInterface;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Spatie\Permission\Middleware\PermissionMiddleware;

class EventParticipantController extends Controller implements HasMiddleware
{
    private EventParticipantRepositoryInterface $eventParticipantRepository;
    private NotificationService $notificationService;

    public function __construct(
        EventParticipantRepositoryInterface $eventParticipantRepository,
        NotificationService $notificationService
    ) {
        $this->eventParticipantRepository = $eventParticipantRepository;
        $this->notificationService = $notificationService;
    }

    public static function middleware()
    {
        return [
            new Middleware(PermissionMiddleware::using(['event-participant-list|event-participant-create|event-participant-edit|event-participant-delete']), only: ['index', 'getAllPaginated', 'show']),

            new Middleware(PermissionMiddleware::using(['event-participant-create']), only: ['store']),

            new Middleware(PermissionMiddleware::using(['event-participant-edit']), only: ['update']),

            new Middleware(PermissionMiddleware::using(['event-participant-delete']), only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        //
        try {
            $eventParticipants = $this->eventParticipantRepository->getAll(
                $request->search,
                $request->limit,
                true
            );

            return ResponseHelper::jsonResponse(true, 'Data peserta event Berhasil Diambil', EventParticipantResource::collection($eventParticipants), 200);
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
        ]);

        try {
            $eventParticipants = $this->eventParticipantRepository->getAllPaginated(
                $request['search'] ?? null,
                $request['row_per_page'],
            );

            return ResponseHelper::jsonResponse(true, 'Data Peserta event Berhasil Diambil', PaginateResource::make($eventParticipants, EventParticipantResource::class), 200);
        } catch (\Exception $e) {
            //throw $th;
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EventParticipantStoreRequest $request)
    {
        $request = $request->validated();

        try {
            //code...
            $eventParticipant = $this->eventParticipantRepository->create($request);

            // Load relationships for notifications
            $eventParticipant->load(['event', 'headOfFamily.user']);

            // Send notifications
            $this->notificationService->eventParticipantRegistered(
                $eventParticipant->event,
                $eventParticipant->headOfFamily
            );

            return ResponseHelper::jsonResponse(true, 'Data peserta event berhasil ditambahkan', new EventParticipantResource($eventParticipant), 201);
        } catch (\Exception $e) {
            //throw $th;
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $eventParticipant = $this->eventParticipantRepository->getById($id);

            if (! $eventParticipant) {
                return ResponseHelper::jsonResponse(false, 'Data peserta event tidak ditemukan', null, 404);
            }

            return ResponseHelper::jsonResponse(true, 'Data Peserta Event berhasil diambil', new EventParticipantResource($eventParticipant), 200);

        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EventParticipantUpdateRequest $request, string $id)
    {
        $request = $request->validated();
        try {
            $eventParticipant = $this->eventParticipantRepository->getById($id);

            if (! $eventParticipant) {
                return ResponseHelper::jsonResponse(false, 'Data peserta event tidak ditemukan', null, 404);
            }
            $eventParticipant = $this->eventParticipantRepository->update($id, $request->validated());

            return ResponseHelper::jsonResponse(true, 'Data Peserta Event berhasil diupdate', new EventParticipantResource($eventParticipant), 200);

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
            $eventParticipant = $this->eventParticipantRepository->getById($id);

            if (! $eventParticipant) {
                return ResponseHelper::jsonResponse(false, 'Data Peserta event tidak ditemukan', null, 404);
            }

            $this->eventParticipantRepository->delete($id);

            return ResponseHelper::jsonResponse(true, 'Data Peserta Event berhasil dihapus', null, 200);

        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }
}
