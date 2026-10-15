<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'login' => 'awa_coulibaly',
            'phone' => '0707070707',
            'email' => 'awa@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ], $overrides);
    }

    public function test_registration_screen_can_be_rendered(): void
    {
        $this->get('/register')->assertOk();
    }

    public function test_new_users_can_register_and_receive_a_confirmation_email(): void
    {
        Notification::fake();

        $response = $this->post('/register', $this->payload());

        $this->assertAuthenticated();
        $response->assertRedirect(route('verification.notice'));

        $user = User::where('email', 'awa@example.com')->firstOrFail();
        $this->assertSame('awa_coulibaly', $user->login);
        $this->assertSame('0707070707', $user->phone);
        $this->assertNull($user->email_verified_at);

        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public static function validPhones(): array
    {
        return [
            '10 chiffres' => ['0707070707'],
            'avec +225' => ['+2250707070707'],
        ];
    }

    #[DataProvider('validPhones')]
    public function test_valid_ivorian_phone_numbers_are_accepted(string $phone): void
    {
        $this->post('/register', $this->payload(['phone' => $phone]))->assertSessionHasNoErrors();

        $this->assertDatabaseHas('users', ['phone' => $phone]);
    }

    public static function invalidPhones(): array
    {
        return [
            'trop court' => ['123'],
            '8 chiffres' => ['07070707'],
            'avec espaces' => ['07 07 07 07 07'],
            'des lettres' => ['abcdefghij'],
            'trop long' => ['07070707070'],
        ];
    }

    #[DataProvider('invalidPhones')]
    public function test_invalid_phone_numbers_are_rejected(string $phone): void
    {
        $this->post('/register', $this->payload(['phone' => $phone]))->assertSessionHasErrors('phone');

        $this->assertDatabaseCount('users', 0);
    }

    public function test_login_must_be_unique(): void
    {
        User::factory()->create(['login' => 'awa_coulibaly']);

        $this->post('/register', $this->payload())->assertSessionHasErrors('login');
    }

    public function test_email_must_be_unique(): void
    {
        User::factory()->create(['email' => 'awa@example.com']);

        $this->post('/register', $this->payload(['login' => 'autre_login']))->assertSessionHasErrors('email');
    }

    public static function invalidLogins(): array
    {
        return [
            'avec un espace' => ['awa coulibaly'],
            'avec un point' => ['awa.coulibaly'],
            'avec un @' => ['awa@home'],
            'vide' => [''],
        ];
    }

    #[DataProvider('invalidLogins')]
    public function test_login_format_is_validated(string $login): void
    {
        $this->post('/register', $this->payload(['login' => $login]))->assertSessionHasErrors('login');
    }

    public function test_password_must_be_confirmed(): void
    {
        $this->post('/register', $this->payload(['password_confirmation' => 'different']))
            ->assertSessionHasErrors('password');
    }

    public function test_password_must_be_long_enough(): void
    {
        $this->post('/register', $this->payload(['password' => 'abc', 'password_confirmation' => 'abc']))
            ->assertSessionHasErrors('password');
    }
}
