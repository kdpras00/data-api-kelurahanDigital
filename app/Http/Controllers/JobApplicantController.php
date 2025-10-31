<?php
namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\JobApplicantStoreRequest;
use App\Http\Requests\JobApplicantUpdateRequest;
use App\Http\Requests\JobApplicantUpdateStatusRequest;
use App\Http\Resources\JobApplicantResource;
use App\Http\Resources\PaginateResource;
use App\Interfaces\JobApplicantRepositoryInterface;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Spatie\Permission\Middleware\PermissionMiddleware;

class JobApplicantController extends Controller implements HasMiddleware
{
    private JobApplicantRepositoryInterface $jobApplicantRepository;
    private NotificationService $notificationService;

    public function __construct(
        JobApplicantRepositoryInterface $jobApplicantRepository,
        NotificationService $notificationService
    ) {
        $this->jobApplicantRepository = $jobApplicantRepository;
        $this->notificationService = $notificationService;
    }

    public static function middleware()
    {
        return [
            new Middleware(PermissionMiddleware::using(['job-applicant-list|job-applicant-create|job-applicant-edit|job-applicant-delete']), only: ['index', 'getAllPaginated', 'show']),

            new Middleware(PermissionMiddleware::using(['job-applicant-create']), only: ['store']),

            new Middleware(PermissionMiddleware::using(['job-applicant-edit']), only: ['update', 'updateStatus']),

            new Middleware(PermissionMiddleware::using(['job-applicant-delete']), only: ['destroy']),
        ];
    }
    public function index(Request $request)
    {
        //
        try {
            $jobApplicants = $this->jobApplicantRepository->getAll(
                $request->search,
                $request->limit,
                true,
                $request->user_id
            );

            return ResponseHelper::jsonResponse(true, 'Data Pelamar Berhasil Diambil', JobApplicantResource::collection($jobApplicants), 200);
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
            'user_id'      => 'nullable|string',
        ]);

        try {
            $jobApplicants = $this->jobApplicantRepository->getAllPaginated(
                $request['search'] ?? null,
                $request['row_per_page'],
                $request['user_id'] ?? null
            );

            return ResponseHelper::jsonResponse(true, 'Data Pelamar Berhasil Diambil', PaginateResource::make($jobApplicants, JobApplicantResource::class), 200);
        } catch (\Exception $e) {
            //throw $th;
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(JobApplicantStoreRequest $request)
    {
        $request = $request->validated();

        try {
            //code...
            $jobApplicant = $this->jobApplicantRepository->create($request);

            // Load relationships for notifications
            $jobApplicant->load(['jobVacancy', 'user.headOfFamily']);

            // Send notifications
            if ($jobApplicant->user && $jobApplicant->user->headOfFamily) {
                $this->notificationService->jobApplicationCreated(
                    $jobApplicant->jobVacancy,
                    $jobApplicant->user->headOfFamily
                );
            }

            return ResponseHelper::jsonResponse(true, 'Data Pelamar berhasil ditambahkan', new JobApplicantResource($jobApplicant), 201);
        } catch (\Exception $e) {
            //throw $th;
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    public function show(string $id)
    {
        //

        try {
            $jobApplicant = $this->jobApplicantRepository->getById($id);

            if (! $jobApplicant) {
                return ResponseHelper::jsonResponse(false, 'Data Pelamar tidak ditemukan', null, 404);
            }

            return ResponseHelper::jsonResponse(true, 'Data Pelamar berhasil diambil', new JobApplicantResource($jobApplicant), 200);

        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(JobApplicantUpdateRequest $request, string $id)
    {
        //

        $request = $request->validated();

        try {

            $jobApplicant = $this->jobApplicantRepository->getById($id);

            if (! $jobApplicant) {
                return ResponseHelper::jsonResponse(false, 'Data Pelamar tidak ditemukan', null, 404);
            }
            $jobApplicant = $this->jobApplicantRepository->update(
                $id,
                $request);

            return ResponseHelper::jsonResponse(true, 'Data Pelamar berhasil diupdate', new JobApplicantResource($jobApplicant), 201);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    public function destroy(string $id)
    {
        try {
            $jobApplicant = $this->jobApplicantRepository->getById($id);

            if (! $jobApplicant) {
                return ResponseHelper::jsonResponse(false, 'Data Pelamar tidak ditemukan', null, 404);
            }

            $jobApplicant = $this->jobApplicantRepository->delete($id);

            return ResponseHelper::jsonResponse(true, 'Data Pelamar berhasil dihapus', null, 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Update the status of a job applicant.
     */
    public function updateStatus(JobApplicantUpdateStatusRequest $request, string $id)
    {
        $request = $request->validated();

        try {
            $jobApplicant = $this->jobApplicantRepository->getById($id);

            if (! $jobApplicant) {
                return ResponseHelper::jsonResponse(false, 'Data Pelamar tidak ditemukan', null, 404);
            }

            $jobApplicant = $this->jobApplicantRepository->update($id, $request);

            // Send notification about status change
            if (isset($request['job_status'])) {
                $this->notificationService->jobApplicationStatusChanged(
                    $jobApplicant->jobVacancy,
                    $jobApplicant->headOfFamily,
                    $request['job_status']
                );
            }

            return ResponseHelper::jsonResponse(true, 'Status pelamar berhasil diupdate', new JobApplicantResource($jobApplicant), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }
}
