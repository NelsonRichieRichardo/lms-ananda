<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\StoreModuleRequest;
use App\Http\Requests\Teacher\UpdateModuleRequest;
use App\Models\Course;
use App\Models\Module;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ModuleController extends Controller
{
    public function store(StoreModuleRequest $request, Course $course): RedirectResponse
    {
        $max = (int) $course->modules()->max('order_position') ?? 0;

        $path = null;
        $original = null;
        if ($request->hasFile('mat_attachment')) {
            $file = $request->file('mat_attachment');
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs("courses/{$course->id}/materials", $filename, 'public');
            $original = $file->getClientOriginalName();
        }

        $course->modules()->create([
            'title' => $request->validated('title'),
            'content' => $request->validated('content'),
            'attachment_path' => $path,
            'attachment_original_name' => $original,
            'order_position' => $max + 1,
        ]);

        return redirect()
            ->route('teacher.courses.show', $course)
            ->with('status', __('Study material added.'));
    }

    public function edit(Course $course, Module $module): View
    {
        $this->authorize('update', $course);
        abort_unless($module->course_id === $course->id, 404);

        return view('teacher.modules.edit', compact('course', 'module'));
    }

    public function update(UpdateModuleRequest $request, Course $course, Module $module): RedirectResponse
    {
        abort_unless($module->course_id === $course->id, 404);

        $payload = [
            'title' => $request->validated('title'),
            'content' => $request->validated('content'),
        ];

        if ($request->boolean('remove_material_attachment')) {
            $module->deleteStoredAttachment();
            $payload['attachment_path'] = null;
            $payload['attachment_original_name'] = null;
        }

        if ($request->hasFile('mat_attachment')) {
            $module->deleteStoredAttachment();
            $file = $request->file('mat_attachment');
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $payload['attachment_path'] = $file->storeAs("courses/{$course->id}/materials", $filename, 'public');
            $payload['attachment_original_name'] = $file->getClientOriginalName();
        }

        $module->update($payload);

        return redirect()
            ->route('teacher.courses.show', $course)
            ->with('status', __('Study material updated.'));
    }

    public function destroy(Course $course, Module $module): RedirectResponse
    {
        $this->authorize('update', $course);
        abort_unless($module->course_id === $course->id, 404);

        $module->delete();

        return redirect()
            ->route('teacher.courses.show', $course)
            ->with('status', __('Study material removed.'));
    }
}
