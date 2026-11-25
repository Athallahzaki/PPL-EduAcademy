<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Instructor\StoreModuleRequest;
use App\Http\Requests\Instructor\UpdateModuleRequest;
use App\Models\Course;
use App\Models\Module;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ModuleController extends Controller
{
    /**
     * Display the curriculum management page for a course.
     */
    public function index(Course $course): View
    {
        Gate::authorize('manage', $course);

        $modules = $course->modules()
            ->with(['chapters' => fn ($q) => $q->orderBy('position')])
            ->orderBy('position')
            ->get();

        return view('instructor.modules.index', compact('course', 'modules'));
    }

    /**
     * Show the form for creating a new module.
     */
    public function create(Course $course): View
    {
        Gate::authorize('manage', $course);

        return view('instructor.modules.create', compact('course'));
    }

    /**
     * Store a newly created module in storage.
     */
    public function store(StoreModuleRequest $request, Course $course): RedirectResponse
    {
        Gate::authorize('manage', $course);

        $course->modules()->create($request->validated());

        return redirect()->route('instructor.courses.modules.index', $course)
            ->with('success', 'Module created successfully as draft.');
    }

    /**
     * Show the form for editing the specified module.
     */
    public function edit(Course $course, Module $module): View
    {
        Gate::authorize('manage', $course);

        return view('instructor.modules.edit', compact('course', 'module'));
    }

    /**
     * Update the specified module in storage.
     */
    public function update(UpdateModuleRequest $request, Course $course, Module $module): RedirectResponse
    {
        Gate::authorize('manage', $course);

        $module->update($request->validated());

        return redirect()->route('instructor.courses.modules.index', $course)
            ->with('success', 'Module updated successfully.');
    }

    /**
     * Remove the specified module from storage.
     */
    public function destroy(Course $course, Module $module): RedirectResponse
    {
        Gate::authorize('manage', $course);

        if (!$module->canBeDeleted()) {
            $reason = $module->deletionBlockedReason() ?? 'This module cannot be deleted.';
            return redirect()->back()->with('error', $reason);
        }

        $module->delete();

        return redirect()->route('instructor.courses.modules.index', $course)
            ->with('success', 'Module deleted successfully.');
    }

    /**
     * Reorder module position up or down.
     */
    public function move(Request $request, Course $course, Module $module): RedirectResponse
    {
        Gate::authorize('manage', $course);

        $validated = $request->validate([
            'direction' => ['required', 'in:up,down'],
        ]);

        $module->move($validated['direction']);

        return redirect()->back()->with('success', 'Module order updated.');
    }

    /**
     * Toggle module published status.
     */
    public function publish(Request $request, Course $course, Module $module): RedirectResponse
    {
        Gate::authorize('manage', $course);

        $validated = $request->validate([
            'is_published' => ['required', 'boolean'],
        ]);

        $module->is_published = (bool) $validated['is_published'];
        $module->save();

        $statusText = $module->is_published ? 'published' : 'unpublished';

        return redirect()->back()->with('success', "Module {$statusText} successfully.");
    }
}
