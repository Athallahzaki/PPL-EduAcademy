<?php

namespace Tests\Feature\Forum;

use App\Models\DiscussionThread;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThreadManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_when_accessing_forum(): void
    {
        $this->get(route('forum.threads.index'))->assertRedirect(route('login'));
        $this->get(route('forum.threads.create'))->assertRedirect(route('login'));
        $this->post(route('forum.threads.store'), [])->assertRedirect(route('login'));
    }

    public function test_authenticated_student_can_view_forum_threads(): void
    {
        $student = User::factory()->student()->active()->create();
        $subject = Subject::factory()->create(['name' => 'Programming', 'slug' => 'programming']);
        $thread = DiscussionThread::factory()->create([
            'subject_id' => $subject->id,
            'title' => 'Belajar Algoritma Pemrograman',
        ]);

        $response = $this->actingAs($student)->get(route('forum.threads.index'));

        $response->assertOk();
        $response->assertSee('Forum Diskusi Komunitas');
        $response->assertSee('Belajar Algoritma Pemrograman');
        $response->assertSee('Programming');
    }

    public function test_student_and_instructor_can_access_forum_via_role_shortcuts(): void
    {
        $student = User::factory()->student()->active()->create();
        $instructor = User::factory()->instructor()->active()->create();

        $this->actingAs($student)->get(route('student.forums.index'))->assertOk();
        $this->actingAs($instructor)->get(route('instructor.forums.index'))->assertOk();
    }

    public function test_user_can_filter_threads_by_subject(): void
    {
        $user = User::factory()->student()->active()->create();
        $subjectA = Subject::factory()->create(['name' => 'Matematika', 'slug' => 'matematika']);
        $subjectB = Subject::factory()->create(['name' => 'Fisika', 'slug' => 'fisika']);

        $threadA = DiscussionThread::factory()->create([
            'subject_id' => $subjectA->id,
            'title' => 'Kalkulus Dasar',
        ]);
        $threadB = DiscussionThread::factory()->create([
            'subject_id' => $subjectB->id,
            'title' => 'Mekanika Kuantum',
        ]);

        $response = $this->actingAs($user)->get(route('forum.threads.index', ['subject' => 'matematika']));

        $response->assertOk();
        $response->assertSee('Kalkulus Dasar');
        $response->assertDontSee('Mekanika Kuantum');
    }

    public function test_user_can_search_threads_by_keyword(): void
    {
        $user = User::factory()->student()->active()->create();
        $subject = Subject::factory()->create();

        DiscussionThread::factory()->create([
            'subject_id' => $subject->id,
            'title' => 'Tutorial Belajar Python Dasar',
            'content' => 'Langkah-langkah setup virtual environment',
        ]);
        DiscussionThread::factory()->create([
            'subject_id' => $subject->id,
            'title' => 'Pengenalan UI/UX Figma',
            'content' => 'Belajar wireframing dan prototyping',
        ]);

        $response = $this->actingAs($user)->get(route('forum.threads.index', ['q' => 'Python']));

        $response->assertOk();
        $response->assertSee('Tutorial Belajar Python Dasar');
        $response->assertDontSee('Pengenalan UI/UX Figma');
    }

    public function test_student_can_create_new_thread_with_valid_subject(): void
    {
        $student = User::factory()->student()->active()->create();
        $subject = Subject::factory()->create();

        $payload = [
            'subject_id' => $subject->id,
            'title' => 'Bagaimana cara menggunakan middleware di Laravel?',
            'content' => 'Saya ingin membatasi akses endpoint tertentu agar hanya bisa diakses oleh role admin.',
        ];

        $response = $this->actingAs($student)->post(route('forum.threads.store'), $payload);

        $this->assertDatabaseHas('discussion_threads', [
            'user_id' => $student->id,
            'subject_id' => $subject->id,
            'title' => 'Bagaimana cara menggunakan middleware di Laravel?',
        ]);

        $thread = DiscussionThread::where('title', $payload['title'])->first();
        $response->assertRedirect(route('forum.threads.show', $thread));
        $response->assertSessionHas('status');
    }

    public function test_thread_creation_validates_required_fields(): void
    {
        $student = User::factory()->student()->active()->create();

        $response = $this->actingAs($student)->post(route('forum.threads.store'), [
            'title' => '',
            'subject_id' => '',
            'content' => '',
        ]);

        $response->assertSessionHasErrors(['title', 'subject_id', 'content']);
    }

    public function test_thread_creation_fails_when_subject_does_not_exist(): void
    {
        $student = User::factory()->student()->active()->create();

        $response = $this->actingAs($student)->post(route('forum.threads.store'), [
            'subject_id' => 99999,
            'title' => 'Judul Topik yang Sah dan Panjang',
            'content' => 'Isi konten topik diskusi yang cukup panjang untuk lolos validasi.',
        ]);

        $response->assertSessionHasErrors('subject_id');
    }

    public function test_user_can_view_thread_detail(): void
    {
        $user = User::factory()->student()->active()->create();
        $subject = Subject::factory()->create();
        $thread = DiscussionThread::factory()->create([
            'subject_id' => $subject->id,
            'title' => 'Diskusi Framework CSS Modern',
            'content' => 'Apakah Tailwind CSS lebih efisien dibanding Bootstrap?',
        ]);

        $response = $this->actingAs($user)->get(route('forum.threads.show', $thread));

        $response->assertOk();
        $response->assertSee('Diskusi Framework CSS Modern');
        $response->assertSee('Apakah Tailwind CSS lebih efisien dibanding Bootstrap?');
    }

    public function test_author_can_update_their_thread(): void
    {
        $student = User::factory()->student()->active()->create();
        $subject1 = Subject::factory()->create();
        $subject2 = Subject::factory()->create();
        $thread = DiscussionThread::factory()->create([
            'user_id' => $student->id,
            'subject_id' => $subject1->id,
            'title' => 'Judul Awal',
            'content' => 'Konten awal sebelum diubah.',
        ]);

        $response = $this->actingAs($student)->put(route('forum.threads.update', $thread), [
            'subject_id' => $subject2->id,
            'title' => 'Judul Telah Diperbarui',
            'content' => 'Konten telah diperbarui dengan penjelasan lebih rinci.',
        ]);

        $response->assertRedirect(route('forum.threads.show', $thread));
        $this->assertDatabaseHas('discussion_threads', [
            'id' => $thread->id,
            'subject_id' => $subject2->id,
            'title' => 'Judul Telah Diperbarui',
        ]);
    }

    public function test_author_can_delete_their_thread(): void
    {
        $student = User::factory()->student()->active()->create();
        $thread = DiscussionThread::factory()->create(['user_id' => $student->id]);

        $response = $this->actingAs($student)->delete(route('forum.threads.destroy', $thread));

        $response->assertRedirect(route('forum.threads.index'));
        $this->assertSoftDeleted('discussion_threads', ['id' => $thread->id]);
    }

    public function test_other_user_cannot_update_or_delete_someone_elses_thread(): void
    {
        $owner = User::factory()->student()->active()->create();
        $otherUser = User::factory()->student()->active()->create();
        $thread = DiscussionThread::factory()->create(['user_id' => $owner->id]);

        // Attempt update
        $this->actingAs($otherUser)
            ->put(route('forum.threads.update', $thread), [
                'subject_id' => $thread->subject_id,
                'title' => 'Judul Dibajak',
                'content' => 'Konten dibajak oleh orang lain.',
            ])
            ->assertForbidden();

        // Attempt delete
        $this->actingAs($otherUser)
            ->delete(route('forum.threads.destroy', $thread))
            ->assertForbidden();

        $this->assertDatabaseHas('discussion_threads', [
            'id' => $thread->id,
            'title' => $thread->title,
        ]);
    }

    public function test_admin_can_update_and_delete_any_thread(): void
    {
        $admin = User::factory()->admin()->create();
        $student = User::factory()->student()->active()->create();
        $thread = DiscussionThread::factory()->create(['user_id' => $student->id]);

        // Admin update
        $this->actingAs($admin)
            ->put(route('forum.threads.update', $thread), [
                'subject_id' => $thread->subject_id,
                'title' => 'Judul Diubah Oleh Admin Moderator',
                'content' => 'Konten disesuaikan oleh administrator.',
            ])
            ->assertRedirect(route('forum.threads.show', $thread));

        $this->assertDatabaseHas('discussion_threads', [
            'id' => $thread->id,
            'title' => 'Judul Diubah Oleh Admin Moderator',
        ]);

        // Admin delete
        $this->actingAs($admin)
            ->delete(route('forum.threads.destroy', $thread))
            ->assertRedirect(route('forum.threads.index'));

        $this->assertSoftDeleted('discussion_threads', ['id' => $thread->id]);
    }
}
