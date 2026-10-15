<?php

namespace Tests\Feature;

use App\Models\Ad;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Illuminate\Filesystem\FilesystemAdapter;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    private function disk(): FilesystemAdapter
{
    return Storage::disk('public');
}

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/profile')->assertOk();
    }

    public function test_guests_cannot_see_the_profile_page(): void
    {
        $this->get('/profile')->assertRedirect(route('login'));
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->from('/profile')->patch('/profile', [
            'login' => 'nouveau_login',
            'email' => $user->email,
            'phone' => '0102030405',
        ]);

        $response->assertSessionHasNoErrors()->assertRedirect('/profile');

        $user->refresh();
        $this->assertSame('nouveau_login', $user->login);
        $this->assertSame('0102030405', $user->phone);
    }

    public function test_keeping_the_same_login_and_email_is_allowed(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->from('/profile')->patch('/profile', [
            'login' => $user->login,
            'email' => $user->email,
            'phone' => $user->phone,
        ])->assertSessionHasNoErrors();
    }

    public function test_email_verification_is_reset_when_the_email_changes(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->from('/profile')->patch('/profile', [
            'login' => $user->login,
            'email' => 'nouvelle@example.com',
            'phone' => $user->phone,
        ])->assertSessionHasNoErrors();

        $this->assertNull($user->refresh()->email_verified_at);
    }

    public function test_email_verification_is_kept_when_the_email_is_unchanged(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->from('/profile')->patch('/profile', [
            'login' => $user->login,
            'email' => $user->email,
            'phone' => '0102030405',
        ]);

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_invalid_phone_is_rejected_on_update(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->from('/profile')->patch('/profile', [
            'login' => $user->login,
            'email' => $user->email,
            'phone' => '123',
        ])->assertSessionHasErrors('phone')->assertRedirect('/profile');
    }

    public function test_login_cannot_be_taken_by_someone_else(): void
    {
        User::factory()->create(['login' => 'deja_pris']);
        $user = User::factory()->create();

        $this->actingAs($user)->from('/profile')->patch('/profile', [
            'login' => 'deja_pris',
            'email' => $user->email,
            'phone' => $user->phone,
        ])->assertSessionHasErrors('login');
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->delete('/profile', ['password' => 'password']);

        $response->assertSessionHasNoErrors()->assertRedirect('/');
        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_correct_password_must_be_provided_to_delete_the_account(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->from('/profile')->delete('/profile', ['password' => 'mauvais'])
            ->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect('/profile');

        $this->assertNotNull($user->fresh());
    }

    public function test_deleting_an_account_removes_its_ads_and_photo_files(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $ad = Ad::factory()->for($user)->create();
        $ad->photos()->create(['path' => 'ads/photo.jpg', 'position' => 1]);
        Storage::disk('public')->put('ads/photo.jpg', 'contenu');

        $this->actingAs($user)->delete('/profile', ['password' => 'password']);

        $this->assertDatabaseCount('ads', 0);
        $this->assertDatabaseCount('ad_photos', 0);
        $this->disk()->assertMissing('ads/photo.jpg');
    }
}
