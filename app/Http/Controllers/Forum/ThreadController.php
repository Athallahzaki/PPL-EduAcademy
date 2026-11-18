<?php

namespace App\Http\Controllers\Forum;

use App\Http\Controllers\Controller;
use App\Http\Requests\Forum\StoreThreadRequest;
use App\Http\Requests\Forum\UpdateThreadRequest;
use App\Models\DiscussionThread;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ThreadController extends Controller
{
    /**
     * Display a listing of forum threads.
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', DiscussionThread::class);

        $query = DiscussionThread::query()
            ->with(['user', 'subject']);

        // Filter by subject
        if ($request->filled('subject')) {
            $query->withSubject($request->input('subject'));
        } elseif ($request->filled('subject_id')) {
            $query->where('subject_id', (int) $request->input('subject_id'));
        }

        // Search in title, content, or author
        if ($request->filled('q')) {
            $query->search($request->input('q'));
        }

        // Sort order
        if ($request->input('sort') === 'oldest') {
            $query->oldest('created_at');
        } else {
            $query->latest('created_at');
        }

        $threads = $query->paginate(10)->withQueryString();
        $subjects = Subject::orderBy('name')->get();

        $selectedSubject = null;
        if ($request->filled('subject')) {
            $selectedSubject = $subjects->firstWhere('slug', $request->input('subject'));
        } elseif ($request->filled('subject_id')) {
            $selectedSubject = $subjects->firstWhere('id', (int) $request->input('subject_id'));
        }

        $totalThreads = DiscussionThread::count();

        return view('forum.threads.index', compact(
            'threads',
            'subjects',
            'selectedSubject',
            'totalThreads'
        ));
    }

    /**
     * Show the form for creating a new thread.
     */
    public function create(): View
    {
        Gate::authorize('create', DiscussionThread::class);

        $subjects = Subject::orderBy('name')->get();

        return view('forum.threads.create', compact('subjects'));
    }

    /**
     * Store a newly created thread in storage.
     */
    public function store(StoreThreadRequest $request): RedirectResponse
    {
        Gate::authorize('create', DiscussionThread::class);

        $thread = DiscussionThread::create([
            'user_id' => $request->user()->id,
            'subject_id' => $request->input('subject_id'),
            'title' => $request->input('title'),
            'content' => $request->input('content'),
        ]);

        return redirect()
            ->route('forum.threads.show', $thread)
            ->with('status', 'Topik diskusi berhasil dipublikasikan.');
    }

    /**
     * Display the specified thread.
     */
    public function show(DiscussionThread $thread): View
    {
        Gate::authorize('view', $thread);

        $thread->load(['user', 'subject']);

        return view('forum.threads.show', compact('thread'));
    }

    /**
     * Show the form for editing the thread.
     */
    public function edit(DiscussionThread $thread): View
    {
        Gate::authorize('update', $thread);

        $subjects = Subject::orderBy('name')->get();

        return view('forum.threads.edit', compact('thread', 'subjects'));
    }

    /**
     * Update the specified thread in storage.
     */
    public function update(UpdateThreadRequest $request, DiscussionThread $thread): RedirectResponse
    {
        Gate::authorize('update', $thread);

        $thread->update([
            'subject_id' => $request->input('subject_id'),
            'title' => $request->input('title'),
            'content' => $request->input('content'),
        ]);

        return redirect()
            ->route('forum.threads.show', $thread)
            ->with('status', 'Topik diskusi berhasil diperbarui.');
    }

    /**
     * Remove the specified thread from storage.
     */
    public function destroy(DiscussionThread $thread): RedirectResponse
    {
        Gate::authorize('delete', $thread);

        $thread->delete();

        return redirect()
            ->route('forum.threads.index')
            ->with('status', 'Topik diskusi berhasil dihapus.');
    }
}
