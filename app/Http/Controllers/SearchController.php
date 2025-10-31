<?php
namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Interfaces\SearchRepositoryInterface;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    private SearchRepositoryInterface $searchRepository;

    public function __construct(SearchRepositoryInterface $searchRepository)
    {
        $this->searchRepository = $searchRepository;
    }

    public function search(Request $request)
    {
        $query = $request->input('q', '');

        if (empty($query)) {
            return ResponseHelper::jsonResponse(true, 'No query provided', [
                'headOfFamilies' => [],
                'socialAssistances' => [],
                'socialAssistanceRecipients' => [],
                'jobVacancies' => [],
                'events' => [],
            ], 200);
        }

        try {
            $results = $this->searchRepository->globalSearch($query);

            return ResponseHelper::jsonResponse(true, 'Search results', $results, 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }
}

