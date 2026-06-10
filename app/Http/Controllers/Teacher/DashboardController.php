<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboardService
    ) {}

    public function __invoke(Request $request): View
    {
        $stats = $this->dashboardService->teacherStats($request->user());

        return view('teacher.dashboard', compact('stats'));
    }
}
