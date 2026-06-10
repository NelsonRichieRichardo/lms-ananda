<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboardService
    ) {}

    public function __invoke(): View
    {
        $stats = $this->dashboardService->superAdminStats();

        return view('admin.dashboard', compact('stats'));
    }
}
