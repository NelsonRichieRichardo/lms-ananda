<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTeacherRequest;
use App\Models\User;
use App\Services\TeacherAccountService;
use App\Support\RoleName;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TeacherController extends Controller
{
    public function __construct(
        private readonly TeacherAccountService $teacherAccountService
    ) {}

    public function index(): View
    {
        $teachers = User::query()
            ->role(RoleName::Teacher)
            ->orderBy('name')
            ->paginate(15);

        return view('admin.teachers.index', compact('teachers'));
    }

    public function create(): View
    {
        return view('admin.teachers.create');
    }

    public function store(StoreTeacherRequest $request): RedirectResponse
    {
        $this->teacherAccountService->createTeacher($request->validated());

        return redirect()
            ->route('admin.teachers.index')
            ->with('status', __('Teacher account created successfully.'));
    }
}
