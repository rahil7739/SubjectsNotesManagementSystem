<?php

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\User;
use App\Notifications\RegistrationRequestNotification;
use App\Notifications\UserAccountNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class MultiAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_self_registration_requires_admin_approval_before_sign_in(): void
    {
        $admin = User::factory()->admin()->create();
        Notification::fake();

        $this->post('/register', [
            'name' => 'Regular User',
            'email' => 'regular@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect(route('login'));

        $user = User::where('email', 'regular@example.com')->firstOrFail();
        $this->assertSame(RoleEnum::User, $user->role);
        $this->assertFalse($user->is_approved);
        $this->assertGuest('web');
        Notification::assertSentTo($admin, RegistrationRequestNotification::class);
        $this->actingAs($admin, 'admin')
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee($user->email)
            ->assertSee('Approve');
        $this->get(route('admin.notifications.index'))->assertOk();

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password123',
        ])->assertSessionHasErrors('email');
        $this->assertGuest('web');

        $this->actingAs($admin, 'admin')
            ->post(route('admin.users.approve', $user))
            ->assertRedirect(route('admin.users.index'));
        $this->assertTrue($user->fresh()->is_approved);
        Notification::assertSentTo($user, UserAccountNotification::class);

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password123',
        ])->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user->fresh(), 'web');
    }

    public function test_admin_created_users_are_approved_and_receive_an_account_email(): void
    {
        $admin = User::factory()->admin()->create();
        Notification::fake();

        $this->actingAs($admin, 'admin')
            ->post(route('admin.users.store'), [
                'name' => 'Admin Created',
                'email' => 'created@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ])
            ->assertRedirect();

        $user = User::where('email', 'created@example.com')->firstOrFail();
        $this->assertTrue($user->is_approved);
        Notification::assertSentTo($user, UserAccountNotification::class);
    }

    public function test_api_registration_waits_for_approval_and_does_not_issue_a_token(): void
    {
        config(['services.api.key' => 'test-api-key']);
        $admin = User::factory()->admin()->create();
        Notification::fake();

        $this->withHeader('X-API-Key', 'test-api-key')
            ->postJson('/api/auth/register', [
                'name' => 'API User',
                'email' => 'api-user@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ])
            ->assertAccepted()
            ->assertJsonPath('message', 'Registration submitted. Administrator approval is required before signing in.');

        $user = User::where('email', 'api-user@example.com')->firstOrFail();
        $this->assertFalse($user->is_approved);
        $this->assertDatabaseCount('api_tokens', 0);
        Notification::assertSentTo($admin, RegistrationRequestNotification::class);

        $this->withHeader('X-API-Key', 'test-api-key')
            ->postJson('/api/auth/login', [
                'email' => $user->email,
                'password' => 'password123',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('email');
    }

    public function test_admin_credentials_cannot_sign_in_through_the_user_portal(): void
    {
        User::factory()->create([
            'email' => 'admin@example.com',
            'password' => 'password123',
        ])->forceFill(['role' => RoleEnum::Admin])->save();

        $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'password123',
        ])->assertSessionHasErrors('email');

        $this->assertGuest('web');
    }

    public function test_regular_users_cannot_sign_in_through_the_admin_portal_or_open_admin_pages(): void
    {
        User::factory()->create([
            'email' => 'user@example.com',
            'password' => 'password123',
        ]);

        $this->post(route('admin.login'), [
            'email' => 'user@example.com',
            'password' => 'password123',
        ])->assertSessionHasErrors('email');

        $this->assertGuest('admin');

        $this->actingAs(User::where('email', 'user@example.com')->firstOrFail(), 'web')
            ->get(route('admin.dashboard'))
            ->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_sign_in_and_access_only_the_admin_dashboard(): void
    {
        User::factory()->create([
            'email' => 'admin@example.com',
            'password' => 'password123',
        ])->forceFill(['role' => RoleEnum::Admin])->save();

        $this->post(route('admin.login'), [
            'email' => 'admin@example.com',
            'password' => 'password123',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs(User::where('email', 'admin@example.com')->first(), 'admin');
        $this->get(route('admin.dashboard'))->assertOk();
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_wrong_role_is_forbidden_even_if_authenticated_on_a_guard(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin, 'web')
            ->get(route('dashboard'))
            ->assertForbidden();
    }
}
