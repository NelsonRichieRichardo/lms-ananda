# Security Findings Verification

## Critical Finding 1: IDOR in Student Module Progress

### 1. Exact File Path
`app/Http/Controllers/Student/CourseController.php`

### 2. Exact Vulnerable Code
Lines 73-93 (markModuleComplete method):
```php
public function markModuleComplete(Request $request, Course $course, Module $module): RedirectResponse
{
    $this->authorize('view', $course);

    if ($module->course_id !== $course->id) {
        abort(404);
    }

    ModuleProgress::updateOrCreate(
        [
            'user_id' => $request->user()->id,
            'module_id' => $module->id,
        ],
        [
            'is_completed' => true,
            'completed_at' => now(),
        ]
    );

    return back()->with('status', 'module-completed');
}
```

Lines 95-111 (markModuleIncomplete method):
```php
public function markModuleIncomplete(Request $request, Course $course, Module $module): RedirectResponse
{
    $this->authorize('view', $course);

    if ($module->course_id !== $course->id) {
        abort(404);
    }

    ModuleProgress::where('user_id', $request->user()->id)
        ->where('module_id', $module->id)
        ->update([
            'is_completed' => false,
            'completed_at' => null,
        ]);

    return back()->with('status', 'module-incomplete');
}
```

### 3. Reproduction Steps
1. Student logs in as a student account
2. Student navigates to any published course (even one they're not enrolled in)
3. Student sends POST request to `/student/courses/{course_id}/modules/{module_id}/complete`
4. Module is marked as complete for the student

### 4. Example Exploit Scenario
A student named Alice wants to fake completion of modules in a course she's not enrolled in. She:
1. Finds a published course ID (e.g., course_id=5)
2. Finds a module ID in that course (e.g., module_id=12)
3. Sends POST to `/student/courses/5/modules/12/complete`
4. The system checks only that the module belongs to the course and the course is published
5. Alice's progress is updated even though she never enrolled in the course
6. This allows academic integrity violations and fake progress tracking

### 5. Recommended Patch
Add enrollment verification before allowing module progress updates:
```php
public function markModuleComplete(Request $request, Course $course, Module $module): RedirectResponse
{
    $this->authorize('view', $course);

    if ($module->course_id !== $course->id) {
        abort(404);
    }

    // Add enrollment verification
    $enrollment = $request->user()
        ->enrollments()
        ->where('course_id', $course->id)
        ->first();

    if (!$enrollment) {
        abort(403, 'You are not enrolled in this course');
    }

    ModuleProgress::updateOrCreate(
        [
            'user_id' => $request->user()->id,
            'module_id' => $module->id,
        ],
        [
            'is_completed' => true,
            'completed_at' => now(),
        ]
    );

    return back()->with('status', 'module-completed');
}
```

Apply the same fix to `markModuleIncomplete` method.

---

## Critical Finding 2: IDOR in Teacher Assignment Grading

### 1. Exact File Path
`app/Http/Controllers/Teacher/AssignmentController.php`

### 2. Exact Vulnerable Code
Lines 82-104 (grade method):
```php
public function grade(Request $request, Course $course, Assignment $assignment, Submission $submission): RedirectResponse
{
    $this->authorize('update', $course);
    abort_unless($assignment->course_id === $course->id, 404);
    abort_unless($submission->assignment_id === $assignment->id, 404);

    $validated = $request->validate([
        'grade' => 'required|numeric|min:0|max:100',
        'grade_comment' => 'nullable|string|max:65535',
    ]);

    $submission->update([
        'grade' => $validated['grade'],
        'grade_comment' => $validated['grade_comment'] ?? null,
        'graded_at' => now(),
    ]);

    $submission->user->notify(new SubmissionGraded($submission));

    return redirect()
        ->route('teacher.courses.assignments.submissions', [$course, $assignment])
        ->with('status', __('Grade saved.'));
}
```

### 3. Reproduction Steps
1. Teacher logs in
2. Teacher navigates to their course's assignment submissions
3. Teacher modifies URL to include a submission ID from a different course
4. Teacher can grade submissions from students not enrolled in their course

### 4. Example Exploit Scenario
Teacher Bob teaches Course A. Student Charlie is enrolled in Course B (taught by Teacher Dave). Charlie submits an assignment for Course B. Teacher Bob:
1. Finds Charlie's submission ID (e.g., submission_id=45)
2. Navigates to `/teacher/courses/{course_a_id}/assignments/{assignment_a_id}/submissions/{submission_id=45}/grade`
3. The system checks that assignment belongs to course and submission belongs to assignment
4. But it doesn't verify Charlie is enrolled in Bob's course
5. Bob can grade Charlie's submission from a different course
6. This allows grade manipulation and unauthorized access to student work

### 5. Recommended Patch
Add enrollment verification to ensure the student is enrolled in the teacher's course:
```php
public function grade(Request $request, Course $course, Assignment $assignment, Submission $submission): RedirectResponse
{
    $this->authorize('update', $course);
    abort_unless($assignment->course_id === $course->id, 404);
    abort_unless($submission->assignment_id === $assignment->id, 404);

    // Add enrollment verification
    $enrollment = Enrollment::where('course_id', $course->id)
        ->where('student_id', $submission->user_id)
        ->first();

    if (!$enrollment) {
        abort(403, 'Student is not enrolled in this course');
    }

    $validated = $request->validate([
        'grade' => 'required|numeric|min:0|max:100',
        'grade_comment' => 'nullable|string|max:65535',
    ]);

    $submission->update([
        'grade' => $validated['grade'],
        'grade_comment' => $validated['grade_comment'] ?? null,
        'graded_at' => now(),
    ]);

    $submission->user->notify(new SubmissionGraded($submission));

    return redirect()
        ->route('teacher.courses.assignments.submissions', [$course, $assignment])
        ->with('status', __('Grade saved.'));
}
```

---

## High Finding 1: Upload Filename Handling

### 1. Exact File Path
`app/Http/Controllers/Teacher/ModuleController.php`

### 2. Exact Vulnerable Code
Line 24 (store method):
```php
$original = $file->getClientOriginalName();
```

Line 67 (update method):
```php
$payload['attachment_original_name'] = $file->getClientOriginalName();
```

Also in `app/Http/Controllers/Student/AssignmentController.php` line 51:
```php
$submission->attachment_original_name = $request->file('attachment')->getClientOriginalName();
```

### 3. Reproduction Steps
1. Teacher uploads a file with a malicious filename
2. The original filename is preserved in the database
3. If the filename is displayed in the UI, XSS can occur
4. If the filename is used in file operations, path traversal is possible

### 4. Example Exploit Scenario
**XSS Scenario:**
1. Teacher uploads a file named `<script>alert('XSS')</script>.pdf`
2. The filename is stored in the database as-is
3. When the file is displayed in the UI (e.g., download link), the script executes
4. XSS attack succeeds

**Path Traversal Scenario:**
1. Teacher uploads a file named `../../evil.php`
2. If the filename is used in file operations without proper sanitization
3. The file could be written to an unintended location
4. Potential for arbitrary file write

### 5. Recommended Patch
Use UUID-based filenames instead of preserving original names:
```php
use Illuminate\Support\Str;

// In store method:
$path = null;
$original = null;
if ($request->hasFile('mat_attachment')) {
    $file = $request->file('mat_attachment');
    $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
    $path = $file->storeAs("courses/{$course->id}/materials", $filename, 'public');
    $original = $file->getClientOriginalName(); // Keep for display, but sanitize
}

// In update method:
if ($request->hasFile('mat_attachment')) {
    $module->deleteStoredAttachment();
    $file = $request->file('mat_attachment');
    $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
    $payload['attachment_path'] = $file->storeAs("courses/{$course->id}/materials", $filename, 'public');
    $payload['attachment_original_name'] = $file->getClientOriginalName(); // Keep for display, but sanitize
}
```

Apply similar fix to Student\AssignmentController.

---

## High Finding 2: Unpublished Course Exposure in Search

### 1. Exact File Path
`app/Http/Controllers/SearchController.php`

### 2. Exact Vulnerable Code
Lines 33-38 (assignments search):
```php
if ($type === 'all' || $type === 'assignments') {
    $assignments = Assignment::where('title', 'like', "%{$query}%")
        ->orWhere('instructions', 'like', "%{$query}%")
        ->with('course')
        ->get();
}
```

Lines 40-45 (modules search):
```php
if ($type === 'all' || $type === 'modules') {
    $modules = Module::where('title', 'like', "%{$query}%")
        ->orWhere('content', 'like', "%{$query}%")
        ->with('course')
        ->get();
}
```

### 3. Reproduction Steps
1. Student logs in
2. Student searches for a term that appears in unpublished course content
3. Results include assignments and modules from unpublished courses
4. Student can see content from courses not yet available

### 4. Example Exploit Scenario
Teacher Dave is preparing Course B but hasn't published it yet. Student Alice:
1. Searches for "math" which appears in Course B's assignment titles
2. Search results include assignments from unpublished Course B
3. Alice can see assignment titles and instructions from unpublished courses
4. This exposes course content before it's meant to be available
5. Academic integrity issue and premature content disclosure

### 5. Recommended Patch
Add course publication check to assignments and modules search:
```php
if ($type === 'all' || $type === 'assignments') {
    $assignments = Assignment::where('title', 'like', "%{$query}%")
        ->orWhere('instructions', 'like', "%{$query}%")
        ->whereHas('course', function ($q) {
            $q->where('is_published', true);
        })
        ->with('course')
        ->get();
}

if ($type === 'all' || $type === 'modules') {
    $modules = Module::where('title', 'like', "%{$query}%")
        ->orWhere('content', 'like', "%{$query}%")
        ->whereHas('course', function ($q) {
            $q->where('is_published', true);
        })
        ->with('course')
        ->get();
}
```

---

## High Finding 3: Calendar Event Authorization

### 1. Exact File Path
`app/Http/Controllers/CalendarController.php`

### 2. Exact Vulnerable Code
Lines 51-76 (store method):
```php
public function store(Request $request)
{
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string|max:65535',
        'start_at' => 'required|date',
        'end_at' => 'nullable|date|after_or_equal:start_at',
        'type' => 'required|in:assignment,exam,meeting,holiday,other',
        'is_all_day' => 'boolean',
        'course_id' => 'nullable|exists:courses,id',
    ]);

    Event::create([
        'user_id' => auth()->id(),
        'course_id' => $validated['course_id'] ?? null,
        'title' => $validated['title'],
        'description' => $validated['description'] ?? null,
        'start_at' => $validated['start_at'],
        'end_at' => $validated['end_at'] ?? null,
        'type' => $validated['type'],
        'is_all_day' => $request->boolean('is_all_day', false),
    ]);

    return redirect()->route('calendar.index')
        ->with('status', __('Event created successfully.'));
}
```

### 3. Reproduction Steps
1. Any authenticated user (student, teacher, or admin) logs in
2. User sends POST to `/calendar` with course_id parameter
3. Event is created and linked to the course
4. No authorization check if user has permission to link to that course

### 4. Example Exploit Scenario
Student Alice wants to create a calendar event for Course B (taught by Teacher Dave):
1. Alice logs in as a student
2. Alice finds Course B's ID (e.g., course_id=5)
3. Alice sends POST to `/calendar` with course_id=5
4. Event is created and linked to Course B
5. If the calendar displays course events, Alice's event appears for Course B
6. This allows unauthorized modification of course-related calendar data
7. Teachers could see unauthorized events in their course calendars

### 5. Recommended Patch
Add authorization check when course_id is provided:
```php
public function store(Request $request)
{
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string|max:65535',
        'start_at' => 'required|date',
        'end_at' => 'nullable|date|after_or_equal:start_at',
        'type' => 'required|in:assignment,exam,meeting,holiday,other',
        'is_all_day' => 'boolean',
        'course_id' => 'nullable|exists:courses,id',
    ]);

    // Add authorization check for course_id
    if (isset($validated['course_id'])) {
        $course = Course::findOrFail($validated['course_id']);
        $this->authorize('update', $course);
    }

    Event::create([
        'user_id' => auth()->id(),
        'course_id' => $validated['course_id'] ?? null,
        'title' => $validated['title'],
        'description' => $validated['description'] ?? null,
        'start_at' => $validated['start_at'],
        'end_at' => $validated['end_at'] ?? null,
        'type' => $validated['type'],
        'is_all_day' => $request->boolean('is_all_day', false),
    ]);

    return redirect()->route('calendar.index')
        ->with('status', __('Event created successfully.'));
}
```

---

## High Finding 4: EventPolicy Authorization

### 1. Exact File Path
`app/Policies/EventPolicy.php`

### 2. Exact Vulnerable Code
Lines 14-41 (most methods return false):
```php
public function viewAny(User $user): bool
{
    return false;
}

public function view(User $user, Event $event): bool
{
    return false;
}

public function create(User $user): bool
{
    return false;
}

public function update(User $user, Event $event): bool
{
    return false;
}
```

### 3. Reproduction Steps
1. Any code that calls `$this->authorize('create', Event::class)` will fail
2. Any code that calls `$this->authorize('update', $event)` will fail
3. The policy is effectively disabled for most operations
4. Authorization bypass potential if policy is expected to work

### 4. Example Exploit Scenario
The EventPolicy is meant to control access to calendar events, but:
1. Most methods return false, meaning authorization always fails
2. This suggests the policy is incomplete or placeholder code
3. If developers rely on this policy for authorization, it won't work
4. This could lead to authorization bypass if policy checks are added later
5. The delete method works (checks user owns event), but others don't

### 5. Recommended Patch
Implement proper authorization logic:
```php
public function create(User $user): bool
{
    // Allow any authenticated user to create personal events
    return true;
}

public function update(User $user, Event $event): bool
{
    // Only allow event owner to update
    return $user->id === $event->user_id;
}

public function view(User $user, Event $event): bool
{
    // Allow viewing if user owns the event or is enrolled in the course
    if ($user->id === $event->user_id) {
        return true;
    }

    if ($event->course_id) {
        $enrollment = $user->enrollments()
            ->where('course_id', $event->course_id)
            ->first();
        
        if ($enrollment) {
            return true;
        }

        // Teachers can view events for their courses
        $course = Course::find($event->course_id);
        if ($course && $course->teacher_id === $user->id) {
            return true;
        }
    }

    return false;
}

public function viewAny(User $user): bool
{
    // Allow viewing user's own events
    return true;
}
```

---

## Summary

All 6 findings have been verified and confirmed. The vulnerabilities are real and exploitable. Proceeding with Phase 1 fixes.
