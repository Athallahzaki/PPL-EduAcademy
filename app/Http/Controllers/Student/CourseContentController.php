<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\Course;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class CourseContentController extends Controller
{
    /**
     * Display the course content outline for enrolled students.
     */
    public function index(Request $request, Course $course): View
    {
        Gate::authorize('access', $course);

        // Constrained eager loading: published modules and their published chapters only
        $modules = $course->modules()
            ->published()
            ->with(['chapters' => fn ($q) => $q->published()->orderBy('position')])
            ->orderBy('position')
            ->get();

        // Target the first visible chapter
        $firstChapter = null;
        foreach ($modules as $module) {
            if ($module->chapters->isNotEmpty()) {
                $firstChapter = $module->chapters->first();
                break;
            }
        }

        return view('student.courses.modules.index', compact('course', 'modules', 'firstChapter'));
    }

    /**
     * Display a specific chapter for student reading.
     */
    public function chapter(Request $request, Course $course, Module $module, Chapter $chapter): View
    {
        Gate::authorize('access', $course);

        // Student visibility rule (§4 / §7.2):
        // Abort 404 unless both module and chapter are published
        if (!$module->is_published || !$chapter->is_published) {
            abort(404);
        }

        // Query sequence of all visible chapters in this course (§7.2)
        $visibleChapters = Chapter::query()
            ->join('modules', 'chapters.module_id', '=', 'modules.id')
            ->where('modules.course_id', $course->id)
            ->where('modules.is_published', true)
            ->where('chapters.is_published', true)
            ->orderBy('modules.position')
            ->orderBy('chapters.position')
            ->select('chapters.*')
            ->get();

        $currentIndex = $visibleChapters->search(fn ($item) => $item->id === $chapter->id);

        $previousChapter = null;
        if ($currentIndex !== false && $currentIndex > 0) {
            $previousChapter = $visibleChapters[$currentIndex - 1];
            $previousChapter->load('module');
        }

        $nextChapter = null;
        if ($currentIndex !== false && $currentIndex < $visibleChapters->count() - 1) {
            $nextChapter = $visibleChapters[$currentIndex + 1];
            $nextChapter->load('module');
        }

        $chapter->setRelation('module', $module);

        return view('student.courses.chapters.show', compact(
            'course',
            'module',
            'chapter',
            'previousChapter',
            'nextChapter'
        ));
    }
}
