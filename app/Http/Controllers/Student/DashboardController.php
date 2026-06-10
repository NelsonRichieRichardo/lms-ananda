<?php

namespace App\Http\Controllers\Student;

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
        $stats = $this->dashboardService->studentStats($request->user());

        return view('student.dashboard', compact('stats'));
    }
}
