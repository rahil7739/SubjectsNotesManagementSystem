<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StudyManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_manage_owned_subjects_and_notes_and_cannot_access_another_users_records(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $ownSubject = $owner->subjects()->create(['name' => 'Math']);
        $otherSubject = $other->subjects()->create(['name' => 'Science']);
        $ownNote = $owner->notes()->create([
            'subject_id' => $ownSubject->id,
            'title' => 'Algebra',
            'body' => 'Equations',
        ]);
        $otherNote = $other->notes()->create([
            'subject_id' => $otherSubject->id,
            'title' => 'Chemistry',
            'body' => 'Atoms',
        ]);

        $this->actingAs($owner, 'web')
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Algebra');

        $this->actingAs($owner, 'web')
            ->put(route('subjects.update', $otherSubject), ['name' => 'Changed'])
            ->assertNotFound();

        $this->actingAs($owner, 'web')
            ->delete(route('notes.destroy', $otherNote))
            ->assertNotFound();

        $this->actingAs($owner, 'web')
            ->delete(route('notes.destroy', $ownNote))
            ->assertRedirect(route('dashboard'));
        $this->assertSoftDeleted('notes', ['id' => $ownNote->id]);
    }

    public function test_admin_can_restore_and_permanently_delete_soft_deleted_records(): void
    {
        $admin = User::factory()->admin()->create();
        $owner = User::factory()->create();
        $subject = $owner->subjects()->create(['name' => 'History']);
        $note = $owner->notes()->create(['subject_id' => $subject->id, 'title' => 'Ancient history']);
        $note->delete();

        $this->actingAs($admin, 'admin')
            ->post(route('admin.notes.restore', $note->id))
            ->assertRedirect();
        $this->assertDatabaseHas('notes', ['id' => $note->id, 'deleted_at' => null]);

        $subject->delete();
        $this->actingAs($admin, 'admin')
            ->delete(route('admin.subjects.force-delete', $subject->id))
            ->assertRedirect();
        $this->assertDatabaseMissing('subjects', ['id' => $subject->id]);
        $this->assertDatabaseMissing('notes', ['id' => $note->id]);
    }

    public function test_admin_can_soft_delete_restore_and_permanently_delete_a_user_and_their_records(): void
    {
        Storage::fake('local');
        $admin = User::factory()->admin()->create();
        $owner = User::factory()->create();
        $subject = $owner->subjects()->create(['name' => 'Geography']);
        $note = $owner->notes()->create([
            'subject_id' => $subject->id,
            'title' => 'Rivers',
        ]);
        $path = UploadedFile::fake()->create('map.pdf', 10, 'application/pdf')->store('notes/'.$note->id, 'local');
        $note->attachments()->create([
            'path' => $path,
            'original_name' => 'map.pdf',
            'mime_type' => 'application/pdf',
            'size' => 10240,
        ]);
        $owner->apiTokens()->create([
            'name' => 'test device',
            'token' => hash('sha256', 'plain-test-token'),
            'expires_at' => now()->addDay(),
        ]);

        $this->actingAs($admin, 'admin')
            ->delete(route('admin.users.destroy', $owner))
            ->assertRedirect(route('admin.users.deleted'));
        $this->assertSoftDeleted('users', ['id' => $owner->id]);
        $this->assertDatabaseMissing('api_tokens', ['user_id' => $owner->id]);
        $this->get(route('admin.users.deleted'))->assertOk()->assertSee($owner->email);

        $this->post(route('admin.users.restore', $owner->id))->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $owner->id, 'deleted_at' => null]);

        $this->delete(route('admin.users.destroy', $owner))->assertRedirect(route('admin.users.deleted'));
        $this->delete(route('admin.users.force-delete', $owner->id))->assertRedirect();
        $this->assertDatabaseMissing('users', ['id' => $owner->id]);
        $this->assertDatabaseMissing('subjects', ['id' => $subject->id]);
        $this->assertDatabaseMissing('notes', ['id' => $note->id]);
        Storage::disk('local')->assertMissing($path);
    }

    public function test_admin_assignments_create_notifications_for_users_and_users_can_mark_them_read(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();

        $this->actingAs($admin, 'admin')
            ->post(route('admin.subjects.assign'), [
                'user_id' => $user->id,
                'name' => 'Biology',
                'description' => 'Life science',
            ])
            ->assertRedirect(route('admin.subjects.index'));

        $subject = $user->subjects()->where('name', 'Biology')->firstOrFail();

        $this->actingAs($admin, 'admin')
            ->post(route('admin.notes.assign'), [
                'user_id' => $user->id,
                'subject_id' => $subject->id,
                'title' => 'Cell structure',
                'body' => 'Review cell parts.',
            ])
            ->assertRedirect(route('admin.notes.index'));

        $this->assertCount(2, $user->fresh()->notifications);
        $this->actingAs($user, 'web')
            ->get(route('notifications.index'))
            ->assertOk()
            ->assertSee('A subject was added to your workspace')
            ->assertSee('A new note was added');

        $notification = $user->fresh()->unreadNotifications()->firstOrFail();
        $targetRoute = $notification->data['kind'] === 'subject'
            ? route('subject.index')
            : route('notes.index');
        $this->actingAs($user, 'web')
            ->post(route('notifications.read', $notification->id))
            ->assertRedirect($targetRoute);

        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_deleting_and_restoring_a_subject_moves_its_active_notes_with_it(): void
    {
        $owner = User::factory()->create();
        $subject = $owner->subjects()->create(['name' => 'Literature']);
        $note = $owner->notes()->create(['subject_id' => $subject->id, 'title' => 'Poetry']);

        $subject->delete();
        $this->assertSoftDeleted('subjects', ['id' => $subject->id]);
        $this->assertSoftDeleted('notes', ['id' => $note->id]);
        $this->assertDatabaseHas('notes', ['id' => $note->id, 'deleted_with_subject' => true]);

        $subject->restore();
        $this->assertDatabaseHas('notes', ['id' => $note->id, 'deleted_at' => null, 'deleted_with_subject' => false]);
    }

    public function test_private_attachments_are_downloaded_only_by_the_note_owner_or_an_admin(): void
    {
        Storage::fake('local');
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $subject = $owner->subjects()->create(['name' => 'Biology']);
        $owner->notes()->create(['subject_id' => $subject->id, 'title' => 'Cells']);

        $this->actingAs($owner, 'web')->post(route('notes.store'), [
            'subject_id' => $subject->id,
            'title' => 'Uploaded note',
            'attachments' => [UploadedFile::fake()->create('cells.pdf', 20, 'application/pdf')],
        ])->assertRedirect(route('dashboard'));
        $attachment = $owner->notes()->where('title', 'Uploaded note')->firstOrFail()->attachments()->first();
        $this->assertNotNull($attachment);

        $this->actingAs($other, 'web')
            ->get(route('attachments.download', $attachment))
            ->assertNotFound();

        $this->actingAs($owner, 'web')
            ->get(route('attachments.download', $attachment))
            ->assertOk();
        Storage::disk('local')->assertExists($attachment->path);
    }

    public function test_api_key_and_hashed_bearer_token_protect_api_access(): void
    {
        config(['services.api.key' => 'test-only-secret']);
        $user = User::factory()->create(['password' => 'password123']);

        $this->getJson('/api/subjects')->assertUnauthorized();

        $login = $this->withHeader('X-API-Key', 'test-only-secret')->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password123',
            'device_name' => 'test',
        ])->assertCreated();
        $token = $login->json('token');

        $this->withHeaders([
            'X-API-Key' => 'test-only-secret',
            'Authorization' => 'Bearer '.$token,
        ])->postJson('/api/subjects', ['name' => 'Physics'])
            ->assertCreated()
            ->assertJsonPath('data.name', 'Physics');

        $this->assertDatabaseHas('api_tokens', ['user_id' => $user->id, 'token' => hash('sha256', $token)]);
        $this->withHeaders([
            'X-API-Key' => 'test-only-secret',
            'Authorization' => 'Bearer '.$token,
        ])->postJson('/api/auth/logout')->assertOk();
        $this->withHeaders([
            'X-API-Key' => 'test-only-secret',
            'Authorization' => 'Bearer '.$token,
        ])->getJson('/api/subjects')->assertUnauthorized();
    }

    public function test_user_and_admin_sessions_can_be_used_and_logged_out_independently(): void
    {
        $user = User::factory()->create(['email' => 'user@example.com', 'password' => 'password123']);
        $admin = User::factory()->admin()->create(['email' => 'admin@example.com', 'password' => 'password123']);

        $this->post(route('login.store'), ['email' => $user->email, 'password' => 'password123'])
            ->assertRedirect(route('dashboard'));
        $this->post(route('admin.login.store'), ['email' => $admin->email, 'password' => 'password123'])
            ->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($user, 'web');
        $this->assertAuthenticatedAs($admin, 'admin');
        $this->post(route('logout'))->assertRedirect(route('login'));
        $this->assertGuest('web');
        $this->assertAuthenticatedAs($admin, 'admin');
        $this->get(route('admin.dashboard'))->assertOk();
    }
}
