<?php

namespace App\Http\Controllers\Instructor;

use App\Enums\ChapterType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Instructor\StoreChapterRequest;
use App\Http\Requests\Instructor\UpdateChapterRequest;
use App\Models\Chapter;
use App\Models\Course;
use App\Models\Module;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ChapterController extends Controller
{
    /**
     * Redirect to modules index.
     */
    public function index(Course $course, Module $module): RedirectResponse
    {
        return redirect()->route('instructor.courses.modules.index', $course);
    }

    /**
     * Show the form for creating a new chapter.
     */
    public function create(Course $course, Module $module): View
    {
        Gate::authorize('manage', $course);

        return view('instructor.chapters.create', compact('course', 'module'));
    }

    /**
     * Store a newly created chapter in storage.
     */
    public function store(StoreChapterRequest $request, Course $course, Module $module): RedirectResponse
    {
        Gate::authorize('manage', $course);

        $data = $request->validated();

        // Enforce content rules (§6.3)
        if ($data['type'] === ChapterType::Text->value) {
            $data['video_url'] = null;
        }

        $module->chapters()->create($data);

        return redirect()->route('instructor.courses.modules.index', $course)
            ->with('success', 'Chapter created successfully as draft.');
    }

    /**
     * Preview chapter for instructor (regardless of publish status).
     */
    public function show(Course $course, Module $module, Chapter $chapter): View
    {
        Gate::authorize('manage', $course);

        return view('instructor.chapters.show', compact('course', 'module', 'chapter'));
    }

    /**
     * Show the form for editing the specified chapter.
     */
    public function edit(Course $course, Module $module, Chapter $chapter): View
    {
        Gate::authorize('manage', $course);

        return view('instructor.chapters.edit', compact('course', 'module', 'chapter'));
    }

    /**
     * Update the specified chapter in storage.
     */
    public function update(UpdateChapterRequest $request, Course $course, Module $module, Chapter $chapter): RedirectResponse
    {
        Gate::authorize('manage', $course);

        $data = $request->validated();

        // Enforce content rules (§6.3)
        if ($data['type'] === ChapterType::Text->value) {
            $data['video_url'] = null;
        }

        $chapter->update($data);

        return redirect()->route('instructor.courses.modules.index', $course)
            ->with('success', 'Chapter updated successfully.');
    }

    /**
     * Remove the specified chapter from storage.
     */
    public function destroy(Course $course, Module $module, Chapter $chapter): RedirectResponse
    {
        Gate::authorize('manage', $course);

        $chapter->delete();

        return redirect()->route('instructor.courses.modules.index', $course)
            ->with('success', 'Chapter deleted successfully.');
    }

    /**
     * Reorder chapter position up or down.
     */
    public function move(Request $request, Course $course, Module $module, Chapter $chapter): RedirectResponse
    {
        Gate::authorize('manage', $course);

        $validated = $request->validate([
            'direction' => ['required', 'in:up,down'],
        ]);

        $chapter->move($validated['direction']);

        return redirect()->back()->with('success', 'Chapter order updated.');
    }

    /**
     * Toggle chapter published status.
     */
    public function publish(Request $request, Course $course, Module $module, Chapter $chapter): RedirectResponse
    {
        Gate::authorize('manage', $course);

        $validated = $request->validate([
            'is_published' => ['required', 'boolean'],
        ]);

        $chapter->is_published = (bool) $validated['is_published'];
        $chapter->save();

        $statusText = $chapter->is_published ? 'published' : 'unpublished';

        return redirect()->back()->with('success', "Chapter {$statusText} successfully.");
    }
}
