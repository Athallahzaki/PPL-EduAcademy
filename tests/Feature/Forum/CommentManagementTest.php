<?php

namespace Tests\Feature\Forum;

use App\Models\Comment;
use App\Models\DiscussionThread;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_post_comment_and_is_redirected_to_login(): void
    {
        $subject = Subject::factory()->create();
        $thread = DiscussionThread::factory()->create(['subject_id' => $subject->id]);

        $response = $this->post(route('forum.threads.comments.store', $thread), [
            'comment' => 'Komentar dari guest tanpa login.',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseEmpty('comments');
    }

    public function test_authenticated_student_can_post_root_comment(): void
    {
        $student = User::factory()->student()->active()->create();
        $subject = Subject::factory()->create();
        $thread = DiscussionThread::factory()->create(['subject_id' => $subject->id]);

        $payload = [
            'comment' => 'Ini adalah tanggapan langsung terhadap isi topik diskusi.',
        ];

        $response = $this->actingAs($student)
            ->post(route('forum.threads.comments.store', $thread), $payload);

        $comment = Comment::where('thread_id', $thread->id)->first();
        $this->assertNotNull($comment);
        $this->assertSame($student->id, $comment->user_id);
        $this->assertNull($comment->parent_id);
        $this->assertSame($payload['comment'], $comment->comment);

        $response->assertRedirect(route('forum.threads.show', $thread) . '#comment-' . $comment->id);
        $response->assertSessionHas('status', 'Tanggapan berhasil dipublikasikan.');
    }

    public function test_authenticated_user_can_post_nested_reply_to_existing_comment(): void
    {
        $student = User::factory()->student()->active()->create();
        $instructor = User::factory()->instructor()->active()->create();
        $subject = Subject::factory()->create();
        $thread = DiscussionThread::factory()->create(['subject_id' => $subject->id]);

        $rootComment = Comment::factory()->create([
            'thread_id' => $thread->id,
            'user_id' => $student->id,
            'parent_id' => null,
            'comment' => 'Komentar awal dari siswa.',
        ]);

        $replyPayload = [
            'parent_id' => $rootComment->id,
            'comment' => 'Balasan penjelasan dari instruktur.',
        ];

        $response = $this->actingAs($instructor)
            ->post(route('forum.threads.comments.store', $thread), $replyPayload);

        $reply = Comment::where('parent_id', $rootComment->id)->first();
        $this->assertNotNull($reply);
        $this->assertSame($instructor->id, $reply->user_id);
        $this->assertSame($thread->id, $reply->thread_id);
        $this->assertSame($replyPayload['comment'], $reply->comment);

        $response->assertRedirect(route('forum.threads.show', $thread) . '#comment-' . $reply->id);
        $response->assertSessionHas('status', 'Balasan berhasil dikirimkan.');
    }

    public function test_comment_creation_validates_required_fields(): void
    {
        $student = User::factory()->student()->active()->create();
        $subject = Subject::factory()->create();
        $thread = DiscussionThread::factory()->create(['subject_id' => $subject->id]);

        // Empty comment
        $response = $this->actingAs($student)
            ->post(route('forum.threads.comments.store', $thread), [
                'comment' => '',
            ]);
        $response->assertSessionHasErrors('comment');

        // Comment too short (< 3 chars)
        $responseShort = $this->actingAs($student)
            ->post(route('forum.threads.comments.store', $thread), [
                'comment' => 'ab',
            ]);
        $responseShort->assertSessionHasErrors('comment');

        // Non-existent parent_id
        $responseParent = $this->actingAs($student)
            ->post(route('forum.threads.comments.store', $thread), [
                'comment' => 'Komentar dengan parent tidak valid.',
                'parent_id' => 99999,
            ]);
        $responseParent->assertSessionHasErrors('parent_id');
    }

    public function test_reply_fails_when_parent_comment_belongs_to_different_thread(): void
    {
        $student = User::factory()->student()->active()->create();
        $subject = Subject::factory()->create();
        $threadA = DiscussionThread::factory()->create(['subject_id' => $subject->id]);
        $threadB = DiscussionThread::factory()->create(['subject_id' => $subject->id]);

        $commentOnThreadB = Comment::factory()->create([
            'thread_id' => $threadB->id,
            'user_id' => $student->id,
        ]);

        // Attempt replying to a comment from Thread B while posting to Thread A
        $response = $this->actingAs($student)
            ->post(route('forum.threads.comments.store', $threadA), [
                'comment' => 'Mencoba membalas komentar dari thread lain.',
                'parent_id' => $commentOnThreadB->id,
            ]);

        $response->assertSessionHasErrors('parent_id');
        $this->assertDatabaseMissing('comments', [
            'thread_id' => $threadA->id,
            'parent_id' => $commentOnThreadB->id,
        ]);
    }

    public function test_author_can_update_their_own_comment(): void
    {
        $student = User::factory()->student()->active()->create();
        $subject = Subject::factory()->create();
        $thread = DiscussionThread::factory()->create(['subject_id' => $subject->id]);
        $comment = Comment::factory()->create([
            'thread_id' => $thread->id,
            'user_id' => $student->id,
            'comment' => 'Komentar lama sebelum diperbaiki.',
        ]);

        $response = $this->actingAs($student)
            ->put(route('forum.comments.update', $comment), [
                'comment' => 'Komentar baru yang sudah direvisi dan diperjelas.',
            ]);

        $response->assertRedirect(route('forum.threads.show', $thread) . '#comment-' . $comment->id);
        $response->assertSessionHas('status', 'Tanggapan berhasil diperbarui.');

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'comment' => 'Komentar baru yang sudah direvisi dan diperjelas.',
        ]);
    }

    public function test_author_can_delete_their_own_comment(): void
    {
        $student = User::factory()->student()->active()->create();
        $subject = Subject::factory()->create();
        $thread = DiscussionThread::factory()->create(['subject_id' => $subject->id]);
        $comment = Comment::factory()->create([
            'thread_id' => $thread->id,
            'user_id' => $student->id,
        ]);

        $response = $this->actingAs($student)
            ->delete(route('forum.comments.destroy', $comment));

        $response->assertRedirect(route('forum.threads.show', $thread));
        $response->assertSessionHas('status', 'Tanggapan berhasil dihapus.');

        $this->assertSoftDeleted('comments', [
            'id' => $comment->id,
        ]);
    }

    public function test_other_user_cannot_update_or_delete_someone_elses_comment(): void
    {
        $author = User::factory()->student()->active()->create();
        $intruder = User::factory()->student()->active()->create();
        $subject = Subject::factory()->create();
        $thread = DiscussionThread::factory()->create(['subject_id' => $subject->id]);
        $comment = Comment::factory()->create([
            'thread_id' => $thread->id,
            'user_id' => $author->id,
            'comment' => 'Komentar milik author asli.',
        ]);

        // Attempt update
        $this->actingAs($intruder)
            ->put(route('forum.comments.update', $comment), [
                'comment' => 'Pembajakan komentar.',
            ])
            ->assertForbidden();

        // Attempt delete
        $this->actingAs($intruder)
            ->delete(route('forum.comments.destroy', $comment))
            ->assertForbidden();

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'comment' => 'Komentar milik author asli.',
            'deleted_at' => null,
        ]);
    }

    public function test_admin_can_update_and_delete_any_comment(): void
    {
        $admin = User::factory()->admin()->active()->create();
        $student = User::factory()->student()->active()->create();
        $subject = Subject::factory()->create();
        $thread = DiscussionThread::factory()->create(['subject_id' => $subject->id]);
        $comment = Comment::factory()->create([
            'thread_id' => $thread->id,
            'user_id' => $student->id,
        ]);

        // Admin update
        $this->actingAs($admin)
            ->put(route('forum.comments.update', $comment), [
                'comment' => 'Komentar telah disunting oleh Admin.',
            ])
            ->assertRedirect(route('forum.threads.show', $thread) . '#comment-' . $comment->id);

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'comment' => 'Komentar telah disunting oleh Admin.',
        ]);

        // Admin delete
        $this->actingAs($admin)
            ->delete(route('forum.comments.destroy', $comment))
            ->assertRedirect(route('forum.threads.show', $thread));

        $this->assertSoftDeleted('comments', [
            'id' => $comment->id,
        ]);
    }

    public function test_suspended_user_cannot_post_or_update_comment(): void
    {
        $suspendedUser = User::factory()->student()->suspended()->create();
        $subject = Subject::factory()->create();
        $thread = DiscussionThread::factory()->create(['subject_id' => $subject->id]);

        $this->actingAs($suspendedUser)
            ->post(route('forum.threads.comments.store', $thread), [
                'comment' => 'Mencoba berkomentar saat akun suspended.',
            ])
            ->assertRedirect(route('login'));

        $this->assertDatabaseEmpty('comments');
    }
}
