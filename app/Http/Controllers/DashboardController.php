<?php
namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Interfaces\DashboardRepositoryInterface;

class DashboardController extends Controller
{
    private DashboardRepositoryInterface $dashboardRepository;

    public function __construct(DashboardRepositoryInterface $dashboardRepository)
    {
        $this->dashboardRepository = $dashboardRepository;
    }

    public function getDashboardData()
    {
        try {
            $data = $this->dashboardRepository->getDashboardData();

            return ResponseHelper::jsonResponse(true, 'Success', $data, 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }
}
