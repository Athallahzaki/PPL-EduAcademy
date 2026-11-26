<?php

namespace Tests\Feature\Forum;

use App\Models\Comment;
use App\Models\DiscussionThread;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class CommentModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_comment_can_be_created_as_root_comment(): void
    {
        $user = User::factory()->student()->active()->create();
        $subject = Subject::factory()->create();
        $thread = DiscussionThread::factory()->create([
            'user_id' => $user->id,
            'subject_id' => $subject->id,
        ]);

        $comment = Comment::create([
            'thread_id' => $thread->id,
            'user_id' => $user->id,
            'parent_id' => null,
            'comment' => 'Ini adalah tanggapan tingkat pertama (root comment).',
        ]);

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'thread_id' => $thread->id,
            'user_id' => $user->id,
            'parent_id' => null,
            'comment' => 'Ini adalah tanggapan tingkat pertama (root comment).',
        ]);

        $this->assertTrue($comment->isRoot());
        $this->assertFalse($comment->isReply());
        $this->assertSame(0, $comment->depth());
        $this->assertTrue($comment->thread->is($thread));
        $this->assertTrue($comment->user->is($user));
    }

    public function test_comment_can_have_nested_replies_and_correct_depth(): void
    {
        $user1 = User::factory()->student()->active()->create();
        $user2 = User::factory()->instructor()->active()->create();
        $subject = Subject::factory()->create();
        $thread = DiscussionThread::factory()->create(['subject_id' => $subject->id]);

        // Level 0: Root comment
        $rootComment = Comment::factory()->create([
            'thread_id' => $thread->id,
            'user_id' => $user1->id,
            'parent_id' => null,
            'comment' => 'Pertanyaan awal dari siswa.',
        ]);

        // Level 1: Reply to root comment
        $replyLevel1 = Comment::factory()->create([
            'thread_id' => $thread->id,
            'user_id' => $user2->id,
            'parent_id' => $rootComment->id,
            'comment' => 'Jawaban balasan dari instruktur.',
        ]);

        // Level 2: Reply to reply
        $replyLevel2 = Comment::factory()->create([
            'thread_id' => $thread->id,
            'user_id' => $user1->id,
            'parent_id' => $replyLevel1->id,
            'comment' => 'Tanggapan balik atas jawaban instruktur.',
        ]);

        $this->assertTrue($rootComment->isRoot());
        $this->assertFalse($rootComment->isReply());
        $this->assertSame(0, $rootComment->depth());

        $this->assertFalse($replyLevel1->isRoot());
        $this->assertTrue($replyLevel1->isReply());
        $this->assertSame(1, $replyLevel1->depth());
        $this->assertTrue($replyLevel1->parent->is($rootComment));

        $this->assertFalse($replyLevel2->isRoot());
        $this->assertTrue($replyLevel2->isReply());
        $this->assertSame(2, $replyLevel2->depth());
        $this->assertTrue($replyLevel2->parent->is($replyLevel1));

        // Check direct replies relationship
        $this->assertCount(1, $rootComment->replies);
        $this->assertTrue($rootComment->replies->first()->is($replyLevel1));
        $this->assertCount(1, $replyLevel1->replies);
        $this->assertTrue($replyLevel1->replies->first()->is($replyLevel2));
    }

    public function test_discussion_thread_relations_to_comments_and_root_comments(): void
    {
        $user = User::factory()->student()->active()->create();
        $subject = Subject::factory()->create();
        $thread = DiscussionThread::factory()->create(['subject_id' => $subject->id]);

        $root1 = Comment::factory()->create([
            'thread_id' => $thread->id,
            'user_id' => $user->id,
            'parent_id' => null,
        ]);
        $root2 = Comment::factory()->create([
            'thread_id' => $thread->id,
            'user_id' => $user->id,
            'parent_id' => null,
        ]);
        $reply = Comment::factory()->create([
            'thread_id' => $thread->id,
            'user_id' => $user->id,
            'parent_id' => $root1->id,
        ]);

        $this->assertCount(3, $thread->comments);
        $this->assertCount(2, $thread->rootComments);
        $this->assertTrue($thread->rootComments->contains($root1));
        $this->assertTrue($thread->rootComments->contains($root2));
        $this->assertFalse($thread->rootComments->contains($reply));
    }

    public function test_user_relation_to_comments(): void
    {
        $user = User::factory()->student()->active()->create();
        $otherUser = User::factory()->student()->active()->create();
        $subject = Subject::factory()->create();
        $thread = DiscussionThread::factory()->create(['subject_id' => $subject->id]);

        $comment1 = Comment::factory()->create([
            'thread_id' => $thread->id,
            'user_id' => $user->id,
        ]);
        $comment2 = Comment::factory()->create([
            'thread_id' => $thread->id,
            'user_id' => $user->id,
        ]);
        Comment::factory()->create([
            'thread_id' => $thread->id,
            'user_id' => $otherUser->id,
        ]);

        $this->assertCount(2, $user->comments);
        $this->assertTrue($user->comments->contains($comment1));
        $this->assertTrue($user->comments->contains($comment2));
    }

    public function test_comment_soft_delete(): void
    {
        $comment = Comment::factory()->create();

        $comment->delete();

        $this->assertSoftDeleted('comments', ['id' => $comment->id]);
        $this->assertNull(Comment::find($comment->id));
        $this->assertNotNull(Comment::withTrashed()->find($comment->id));
    }

    public function test_comment_policy_authorization(): void
    {
        $author = User::factory()->student()->active()->create();
        $otherUser = User::factory()->student()->active()->create();
        $admin = User::factory()->admin()->active()->create();
        $suspendedUser = User::factory()->student()->suspended()->create();

        $comment = Comment::factory()->create(['user_id' => $author->id]);

        // Create authorization
        $this->assertTrue(Gate::forUser($author)->allows('create', Comment::class));
        $this->assertTrue(Gate::forUser($admin)->allows('create', Comment::class));
        $this->assertFalse(Gate::forUser($suspendedUser)->allows('create', Comment::class));

        // Update authorization
        $this->assertTrue(Gate::forUser($author)->allows('update', $comment));
        $this->assertTrue(Gate::forUser($admin)->allows('update', $comment));
        $this->assertFalse(Gate::forUser($otherUser)->allows('update', $comment));

        // Delete authorization
        $this->assertTrue(Gate::forUser($author)->allows('delete', $comment));
        $this->assertTrue(Gate::forUser($admin)->allows('delete', $comment));
        $this->assertFalse(Gate::forUser($otherUser)->allows('delete', $comment));
    }
}
