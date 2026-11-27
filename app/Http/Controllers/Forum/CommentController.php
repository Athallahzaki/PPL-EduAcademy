<?php

namespace App\Http\Controllers\Forum;

use App\Http\Controllers\Controller;
use App\Http\Requests\Forum\StoreCommentRequest;
use App\Http\Requests\Forum\UpdateCommentRequest;
use App\Models\Comment;
use App\Models\DiscussionThread;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class CommentController extends Controller
{
    /**
     * Store a newly created comment or nested reply.
     */
    public function store(StoreCommentRequest $request, DiscussionThread $thread): RedirectResponse
    {
        Gate::authorize('create', Comment::class);

        $comment = Comment::create([
            'thread_id' => $thread->id,
            'user_id' => $request->user()->id,
            'parent_id' => $request->input('parent_id'),
            'comment' => $request->input('comment'),
        ]);

        $message = $comment->isReply()
            ? 'Balasan berhasil dikirimkan.'
            : 'Tanggapan berhasil dipublikasikan.';

        return redirect()
            ->to(route('forum.threads.show', $thread) . '#comment-' . $comment->id)
            ->with('status', $message);
    }

    /**
     * Update the specified comment in storage.
     */
    public function update(UpdateCommentRequest $request, Comment $comment): RedirectResponse
    {
        Gate::authorize('update', $comment);

        $comment->update([
            'comment' => $request->input('comment'),
        ]);

        return redirect()
            ->to(route('forum.threads.show', $comment->thread) . '#comment-' . $comment->id)
            ->with('status', 'Tanggapan berhasil diperbarui.');
    }

    /**
     * Remove the specified comment from storage (soft delete).
     */
    public function destroy(Comment $comment): RedirectResponse
    {
        Gate::authorize('delete', $comment);

        $thread = $comment->thread;
        $comment->delete();

        return redirect()
            ->route('forum.threads.show', $thread)
            ->with('status', 'Tanggapan berhasil dihapus.');
    }
}
