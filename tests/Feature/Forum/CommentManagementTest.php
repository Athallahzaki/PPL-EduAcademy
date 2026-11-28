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
            'comment' => 'This is a direct response to the discussion topic.',
        ];

        $response = $this->actingAs($student)
            ->post(route('forum.threads.comments.store', $thread), $payload);

        $comment = Comment::where('thread_id', $thread->id)->first();
        $this->assertNotNull($comment);
        $this->assertSame($student->id, $comment->user_id);
        $this->assertNull($comment->parent_id);
        $this->assertSame($payload['comment'], $comment->comment);

        $response->assertRedirect(route('forum.threads.show', $thread) . '#comment-' . $comment->id);
        $response->assertSessionHas('status', 'Comment posted successfully.');
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
            'comment' => 'Initial comment by student.',
        ]);

        $replyPayload = [
            'parent_id' => $rootComment->id,
            'comment' => 'Helpful explanation reply from instructor.',
        ];

        $response = $this->actingAs($instructor)
            ->post(route('forum.threads.comments.store', $thread), $replyPayload);

        $reply = Comment::where('parent_id', $rootComment->id)->first();
        $this->assertNotNull($reply);
        $this->assertSame($instructor->id, $reply->user_id);
        $this->assertSame($thread->id, $reply->thread_id);
        $this->assertSame($replyPayload['comment'], $reply->comment);

        $response->assertRedirect(route('forum.threads.show', $thread) . '#comment-' . $reply->id);
        $response->assertSessionHas('status', 'Reply submitted successfully.');
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
                'comment' => 'Comment with invalid parent.',
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
                'comment' => 'Trying to reply across different threads.',
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
            'comment' => 'Old comment text.',
        ]);

        $response = $this->actingAs($student)
            ->put(route('forum.comments.update', $comment), [
                'comment' => 'Updated comment text with clearer details.',
            ]);

        $response->assertRedirect(route('forum.threads.show', $thread) . '#comment-' . $comment->id);
        $response->assertSessionHas('status', 'Comment updated successfully.');

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'comment' => 'Updated comment text with clearer details.',
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
        $response->assertSessionHas('status', 'Comment deleted successfully.');

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
            'comment' => 'Original author comment.',
        ]);

        // Attempt update
        $this->actingAs($intruder)
            ->put(route('forum.comments.update', $comment), [
                'comment' => 'Hijacked comment.',
            ])
            ->assertForbidden();

        // Attempt delete
        $this->actingAs($intruder)
            ->delete(route('forum.comments.destroy', $comment))
            ->assertForbidden();

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'comment' => 'Original author comment.',
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
                'comment' => 'Comment edited by Admin.',
            ])
            ->assertRedirect(route('forum.threads.show', $thread) . '#comment-' . $comment->id);

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'comment' => 'Comment edited by Admin.',
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
                'comment' => 'Trying to post while suspended.',
            ])
            ->assertRedirect(route('login'));

        $this->assertDatabaseEmpty('comments');
    }

    public function test_thread_show_page_renders_comment_tree_and_nested_replies(): void
    {
        $student = User::factory()->student()->active()->create(['name' => 'Alice Student']);
        $instructor = User::factory()->instructor()->active()->create(['name' => 'Bob Instructor']);
        $subject = Subject::factory()->create();
        $thread = DiscussionThread::factory()->create(['subject_id' => $subject->id, 'title' => 'Tree Test Topic']);

        $root = Comment::factory()->create([
            'thread_id' => $thread->id,
            'user_id' => $student->id,
            'parent_id' => null,
            'comment' => 'Root question from Alice.',
        ]);

        $reply = Comment::factory()->create([
            'thread_id' => $thread->id,
            'user_id' => $instructor->id,
            'parent_id' => $root->id,
            'comment' => 'Helpful reply from Bob.',
        ]);

        $response = $this->actingAs($student)->get(route('forum.threads.show', $thread));

        $response->assertOk();
        $response->assertSee('Comments & Responses');
        $response->assertSee('Alice Student');
        $response->assertSee('Root question from Alice.');
        $response->assertSee('Bob Instructor');
        $response->assertSee('Helpful reply from Bob.');
        $response->assertSee('Reply');
    }

    public function test_thread_index_page_displays_comment_count_and_supports_popular_sort(): void
    {
        $user = User::factory()->student()->active()->create();
        $subject = Subject::factory()->create();
        $thread1 = DiscussionThread::factory()->create(['subject_id' => $subject->id, 'title' => 'Thread With Many Comments']);
        $thread2 = DiscussionThread::factory()->create(['subject_id' => $subject->id, 'title' => 'Thread Without Comments']);

        Comment::factory()->count(3)->create(['thread_id' => $thread1->id]);

        $response = $this->actingAs($user)->get(route('forum.threads.index'));
        $response->assertOk();
        $response->assertSee('3 replies');
        $response->assertSee('0 replies');

        $popularResponse = $this->actingAs($user)->get(route('forum.threads.index', ['sort' => 'popular']));
        $popularResponse->assertOk();
        $popularResponse->assertSeeInOrder(['Thread With Many Comments', 'Thread Without Comments']);
    }

    public function test_student_dashboard_displays_recent_thread_with_comment_count(): void
    {
        $student = User::factory()->student()->active()->create([
            'onboarded_at' => now(),
            'must_change_password' => false,
        ]);
        $subject = Subject::factory()->create();
        $thread = DiscussionThread::factory()->create([
            'subject_id' => $subject->id,
            'title' => 'Dashboard Visible Thread',
        ]);
        Comment::factory()->count(2)->create(['thread_id' => $thread->id]);

        $response = $this->actingAs($student)->get(route('student.dashboard'));
        $response->assertOk();
        $response->assertSee('Dashboard Visible Thread');
        $response->assertSee('2');
    }
}
