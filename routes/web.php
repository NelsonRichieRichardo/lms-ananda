<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\StudentController as AdminStudentController;
use App\Http\Controllers\Admin\TeacherController as AdminTeacherController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\Student\AnnouncementReplyController;
use App\Http\Controllers\Student\AssignmentController as StudentAssignmentController;
use App\Http\Controllers\Student\CourseController as StudentCourseController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\GradebookController as StudentGradebookController;
use App\Http\Controllers\Teacher\AnnouncementController;
use App\Http\Controllers\Teacher\AssignmentController as TeacherAssignmentController;
use App\Http\Controllers\Teacher\CourseController as TeacherCourseController;
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboardController;
use App\Http\Controllers\Teacher\GradebookController as TeacherGradebookController;
use App\Http\Controllers\Teacher\ModuleController as TeacherModuleController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');


Route::get('/dashboard', function () {
    return redirect()->to(Auth::user()->dashboardUrl());
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified', 'role:super-admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');
    Route::get('/students', [AdminStudentController::class, 'index'])->name('students.index');
    Route::get('/students/create', [AdminStudentController::class, 'create'])->name('students.create');
    Route::post('/students', [AdminStudentController::class, 'store'])->name('students.store');
    Route::get('/students/import', [AdminStudentController::class, 'importForm'])->name('students.import');
    Route::post('/students/import', [AdminStudentController::class, 'importStore'])->name('students.import.store');
    Route::get('/teachers', [AdminTeacherController::class, 'index'])->name('teachers.index');
    Route::get('/teachers/create', [AdminTeacherController::class, 'create'])->name('teachers.create');
    Route::post('/teachers', [AdminTeacherController::class, 'store'])->name('teachers.store');
});

Route::middleware(['auth', 'verified', 'role:teacher'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/dashboard', TeacherDashboardController::class)->name('dashboard');
    Route::resource('courses', TeacherCourseController::class);
    Route::post('courses/{course}/modules', [TeacherModuleController::class, 'store'])->name('courses.modules.store');
    Route::get('courses/{course}/modules/{module}/edit', [TeacherModuleController::class, 'edit'])->name('courses.modules.edit');
    Route::patch('courses/{course}/modules/{module}', [TeacherModuleController::class, 'update'])->name('courses.modules.update');
    Route::delete('courses/{course}/modules/{module}', [TeacherModuleController::class, 'destroy'])->name('courses.modules.destroy');
    Route::post('courses/{course}/assignments', [TeacherAssignmentController::class, 'store'])->name('courses.assignments.store');
    Route::get('courses/{course}/assignments/{assignment}/edit', [TeacherAssignmentController::class, 'edit'])->name('courses.assignments.edit');
    Route::patch('courses/{course}/assignments/{assignment}', [TeacherAssignmentController::class, 'update'])->name('courses.assignments.update');
    Route::delete('courses/{course}/assignments/{assignment}', [TeacherAssignmentController::class, 'destroy'])->name('courses.assignments.destroy');
    Route::get('courses/{course}/assignments/{assignment}/submissions', [TeacherAssignmentController::class, 'submissions'])->name('courses.assignments.submissions');
    Route::post('courses/{course}/assignments/{assignment}/submissions/{submission}/grade', [TeacherAssignmentController::class, 'grade'])->name('courses.assignments.submissions.grade');
    Route::get('courses/{course}/announcements', [AnnouncementController::class, 'index'])->name('courses.announcements.index');
    Route::get('courses/{course}/announcements/create', [AnnouncementController::class, 'create'])->name('courses.announcements.create');
    Route::post('courses/{course}/announcements', [AnnouncementController::class, 'store'])->name('courses.announcements.store');
    Route::get('courses/{course}/announcements/{announcement}/edit', [AnnouncementController::class, 'edit'])->name('courses.announcements.edit');
    Route::patch('courses/{course}/announcements/{announcement}', [AnnouncementController::class, 'update'])->name('courses.announcements.update');
    Route::delete('courses/{course}/announcements/{announcement}', [AnnouncementController::class, 'destroy'])->name('courses.announcements.destroy');
    Route::get('/gradebook', [TeacherGradebookController::class, 'index'])->name('gradebook.index');
    Route::get('/gradebook/{course}', [TeacherGradebookController::class, 'show'])->name('gradebook.show');
});

Route::middleware(['auth', 'verified', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', StudentDashboardController::class)->name('dashboard');
    Route::get('/courses', [StudentCourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/{course}', [StudentCourseController::class, 'show'])->name('courses.show');
    Route::post('/courses/{course}/enroll', [StudentCourseController::class, 'enroll'])->name('courses.enroll');
    Route::post('/courses/{course}/modules/{module}/complete', [StudentCourseController::class, 'markModuleComplete'])->name('courses.modules.complete');
    Route::post('/courses/{course}/modules/{module}/incomplete', [StudentCourseController::class, 'markModuleIncomplete'])->name('courses.modules.incomplete');
    Route::get('/courses/{course}/assignments/{assignment}', [StudentAssignmentController::class, 'show'])->name('courses.assignments.show');
    Route::post('/courses/{course}/assignments/{assignment}', [StudentAssignmentController::class, 'submit'])->name('courses.assignments.submit');
    Route::post('/courses/{course}/announcements/{announcement}/replies', [AnnouncementReplyController::class, 'store'])->name('courses.announcements.replies.store');
    Route::delete('/courses/{course}/announcements/{announcement}/replies/{reply}', [AnnouncementReplyController::class, 'destroy'])->name('courses.announcements.replies.destroy');
    Route::get('/gradebook', [StudentGradebookController::class, 'index'])->name('gradebook.index');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
    Route::post('/calendar', [CalendarController::class, 'store'])->name('calendar.store');
    Route::delete('/calendar/{event}', [CalendarController::class, 'destroy'])->name('calendar.destroy');
    Route::get('/search', [SearchController::class, 'index'])->name('search.index');
});

require __DIR__.'/auth.php';
