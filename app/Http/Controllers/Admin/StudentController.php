<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BulkImportStudentsRequest;
use App\Http\Requests\Admin\StoreStudentRequest;
use App\Models\User;
use App\Services\StudentAccountService;
use App\Support\RoleName;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function __construct(
        private readonly StudentAccountService $studentAccountService
    ) {}

    public function index(): View
    {
        $students = User::query()
            ->role(RoleName::Student)
            ->orderBy('student_id')
            ->paginate(15);

        return view('admin.students.index', compact('students'));
    }

    public function create(): View
    {
        return view('admin.students.create');
    }

    public function store(StoreStudentRequest $request): RedirectResponse
    {
        $this->studentAccountService->createStudent($request->validated());

        return redirect()
            ->route('admin.students.index')
            ->with('status', __('Student account created. They log in with their Student ID and password DDMMYYYY from their date of birth.'));
    }

    public function importForm(): View
    {
        return view('admin.students.import');
    }

    public function importStore(BulkImportStudentsRequest $request): RedirectResponse
    {
        $result = $this->studentAccountService->importFromCsvContent($request->csvPayload());

        $message = trans_choice(
            '{0} No new students imported.|[1] One student imported.|[2,*] :count students imported.',
            $result->created,
            ['count' => $result->created]
        );

        return redirect()
            ->route('admin.students.index')
            ->with('status', $message)
            ->with('import_errors', $result->errors);
    }
}
