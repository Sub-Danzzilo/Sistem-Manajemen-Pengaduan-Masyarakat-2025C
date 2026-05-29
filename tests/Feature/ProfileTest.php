<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('profile.edit', [
                'account' => \Illuminate\Support\Str::slug($user->name),
                'role' => strtolower($user->role)
            ]));

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();
        $originalSlug = \Illuminate\Support\Str::slug($user->name);

        $response = $this
            ->actingAs($user)
            ->patch(route('profile.update', [
                'account' => $originalSlug,
                'role' => strtolower($user->role)
            ]), [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('profile.edit', [
                'account' => $originalSlug,
                'role' => strtolower($user->role)
            ]));

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->create();
        $originalSlug = \Illuminate\Support\Str::slug($user->name);

        $response = $this
            ->actingAs($user)
            ->patch(route('profile.update', [
                'account' => $originalSlug,
                'role' => strtolower($user->role)
            ]), [
                'name' => 'Test User',
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('profile.edit', [
                'account' => $originalSlug,
                'role' => strtolower($user->role)
            ]));

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete(route('profile.destroy', [
                'account' => \Illuminate\Support\Str::slug($user->name),
                'role' => strtolower($user->role)
            ]), [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create();

        $profileEditUrl = route('profile.edit', [
            'account' => \Illuminate\Support\Str::slug($user->name),
            'role' => strtolower($user->role)
        ]);

        $response = $this
            ->actingAs($user)
            ->from($profileEditUrl)
            ->delete(route('profile.destroy', [
                'account' => \Illuminate\Support\Str::slug($user->name),
                'role' => strtolower($user->role)
            ]), [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect($profileEditUrl);

        $this->assertNotNull($user->fresh());
    }
}
