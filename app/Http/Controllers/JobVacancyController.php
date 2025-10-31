<?php
namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\JobVacancyStoreRequest;
use App\Http\Requests\JobVacancyUpdateRequest;
use App\Http\Resources\JobVacancyResource;
use App\Http\Resources\PaginateResource;
use App\Interfaces\JobVacancyRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Spatie\Permission\Middleware\PermissionMiddleware;

class JobVacancyController extends Controller implements HasMiddleware
{

    private JobVacancyRepositoryInterface $jobVacancyRepository;

    public function __construct(JobVacancyRepositoryInterface $jobVacancyRepository)
    {
        $this->jobVacancyRepository = $jobVacancyRepository;
    }

    public static function middleware()
    {
        return [
            new Middleware(PermissionMiddleware::using(['job-vacancy-list|job-vacancy-create|job-vacancy-edit|job-vacancy-delete']), only: ['index', 'getAllPaginated', 'show']),

            new Middleware(PermissionMiddleware::using(['job-vacancy-create']), only: ['store']),

            new Middleware(PermissionMiddleware::using(['job-vacancy-edit']), only: ['update']),

            new Middleware(PermissionMiddleware::using(['job-vacancy-delete']), only: ['destroy']),
        ];
    }
    public function index(Request $request)
    {
        //
        try {
            $jobVacancies = $this->jobVacancyRepository->getAll(
                $request->search,
                $request->status,
                $request->limit,
                true
            );

            return ResponseHelper::jsonResponse(true, 'Data loker Berhasil Diambil', JobVacancyResource::collection($jobVacancies), 200);
        } catch (\Exception $e) {
            //throw $th;
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    public function getAllPaginated(Request $request)
    {
        //
        $request = $request->validate([
            'search'           => 'nullable|string',
            'status'           => 'nullable|string',
            'row_per_page'     => 'required|integer',
            'page'             => 'nullable|integer',
            'job_status'       => 'nullable|string|in:open,closed,filled',
            'company_in_charge'=> 'nullable|string',
            'salary_min'       => 'nullable|numeric',
            'salary_max'       => 'nullable|numeric',
        ]);

        try {
            // Pisahkan filter dari parameter lainnya
            $filters = [
                'job_status'        => $request['job_status'] ?? null,
                'company_in_charge' => $request['company_in_charge'] ?? null,
                'salary_min'        => $request['salary_min'] ?? null,
                'salary_max'        => $request['salary_max'] ?? null,
            ];

            $jobVacancies = $this->jobVacancyRepository->getAllPaginated(
                $request['search'] ?? null,
                $request['status'] ?? null,
                $request['row_per_page'],
                $filters
            );

            return ResponseHelper::jsonResponse(true, 'Data loker Berhasil Diambil', PaginateResource::make($jobVacancies, JobVacancyResource::class), 200);
        } catch (\Exception $e) {
            //throw $th;
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(JobVacancyStoreRequest $request)
    {
        //
        $request = $request->validated();

        try {
            $jobVacancy = $this->jobVacancyRepository->create($request);

            return ResponseHelper::jsonResponse(true, 'Data loker berhasil ditambahkan', new JobVacancyResource($jobVacancy), 201);
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
            $jobVacancy = $this->jobVacancyRepository->getById($id);

            if (! $jobVacancy) {
                return ResponseHelper::jsonResponse(false, 'Data loker tidak ditemukan', null, 404);
            }

            return ResponseHelper::jsonResponse(true, 'Data loker berhasil diambil', new JobVacancyResource($jobVacancy), 200);

        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(JobVacancyUpdateRequest $request, string $id)
    {
        $request = $request->validated();

        try {

            $jobVacancy = $this->jobVacancyRepository->getById($id);

            if (! $jobVacancy) {
                return ResponseHelper::jsonResponse(false, 'Data loker tidak ditemukan', null, 404);
            }
            $jobVacancy = $this->jobVacancyRepository->update(
                $id,
                $request);

            return ResponseHelper::jsonResponse(true, 'Data loker berhasil diupdate', new JobVacancyResource($jobVacancy), 201);
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
            $jobVacancy = $this->jobVacancyRepository->getById($id);

            if (! $jobVacancy) {
                return ResponseHelper::jsonResponse(false, 'Data loker tidak ditemukan', null, 404);
            }

            $jobVacancy = $this->jobVacancyRepository->delete($id);

            return ResponseHelper::jsonResponse(true, 'Data loker berhasil dihapus', null, 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }
}
