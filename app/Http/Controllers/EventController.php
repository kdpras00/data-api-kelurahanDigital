<?php
namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\EventStoreRequest;
use App\Http\Requests\EventUpdateRequest;
use App\Http\Resources\EventResource;
use App\Http\Resources\PaginateResource;
use App\Interfaces\EventRepositoryInterface;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Spatie\Permission\Middleware\PermissionMiddleware;

class EventController extends Controller implements HasMiddleware
{
    private EventRepositoryInterface $eventRepository;
    private NotificationService $notificationService;

    public function __construct(
        EventRepositoryInterface $eventRepository,
        NotificationService $notificationService
    ) {
        $this->eventRepository = $eventRepository;
        $this->notificationService = $notificationService;
    }

    public static function middleware()
    {
        return [
            new Middleware(PermissionMiddleware::using(['event-list|event-create|event-edit|event-delete']), only: ['index', 'getAllPaginated', 'show']),

            new Middleware(PermissionMiddleware::using(['event-create']), only: ['store']),

            new Middleware(PermissionMiddleware::using(['event-edit']), only: ['update']),

            new Middleware(PermissionMiddleware::using(['event-delete']), only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        //
        try {
            $events = $this->eventRepository->getAll(
                $request->search,
                $request->limit,
                true
            );

            return ResponseHelper::jsonResponse(true, 'Data Event Berhasil Diambil', EventResource::collection($events), 200);
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
            'is_active'    => 'nullable|boolean',
            'date_from'    => 'nullable|date',
            'date_to'      => 'nullable|date',
            'price_min'    => 'nullable|numeric',
            'price_max'    => 'nullable|numeric',
        ]);

        try {
            // Pisahkan filter dari parameter lainnya
            $filters = [
                'is_active' => $request['is_active'] ?? null,
                'date_from' => $request['date_from'] ?? null,
                'date_to'   => $request['date_to'] ?? null,
                'price_min' => $request['price_min'] ?? null,
                'price_max' => $request['price_max'] ?? null,
            ];

            $events = $this->eventRepository->getAllPaginated(
                $request['search'] ?? null,
                $request['row_per_page'],
                $filters
            );

            return ResponseHelper::jsonResponse(true, 'Data Event Berhasil Diambil', PaginateResource::make($events, EventResource::class), 200);
        } catch (\Exception $e) {
            //throw $th;
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    public function store(EventStoreRequest $request)
    {
        //
        $request = $request->validated();

        try {
            $event = $this->eventRepository->create($request);

            // Notify all users about new event
            $this->notificationService->eventCreated($event);

            return ResponseHelper::jsonResponse(true, 'Data event berhasil ditambahkan', new EventResource($event), 201);
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
            $event = $this->eventRepository->getById($id);

            if (! $event) {
                return ResponseHelper::jsonResponse(false, 'Data event tidak ditemukan', null, 404);
            }

            return ResponseHelper::jsonResponse(true, 'Data event berhasil diambil', new EventResource($event), 200);

        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EventUpdateRequest $request, string $id)
    {
        //
        $request = $request->validated();

        try {

            $event = $this->eventRepository->getById($id);

            if (! $event) {
                return ResponseHelper::jsonResponse(false, 'Data event tidak ditemukan', null, 404);
            }
            $event = $this->eventRepository->update(
                $id,
                $request);

            return ResponseHelper::jsonResponse(true, 'Data event berhasil diupdate', new EventResource($event), 201);
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
            $event = $this->eventRepository->getById($id);

            if (! $event) {
                return ResponseHelper::jsonResponse(false, 'Data event tidak ditemukan', null, 404);
            }

            $event = $this->eventRepository->delete($id);

            return ResponseHelper::jsonResponse(true, 'Data event berhasil dihapus', null, 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }
}
