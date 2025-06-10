<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeMail;

class UserRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_through_form()
    {
        Mail::fake();

        $response = $this->withoutMiddleware()
            ->post('/register', [
                'name' => 'Jane Doe',
                'email' => 'jane@example.com',
                'password' => 'secret123',
                'password_confirmation' => 'secret123',
            ]);

        $response->assertRedirect('/home');

        $this->assertDatabaseHas('users', [
            'email' => 'jane@example.com'
        ]);

        Mail::assertSent(WelcomeMail::class);
    }

    public function test_registers_user_via_auth_service()
{
    $mock = Mockery::mock(AuthService::class);
    $mock->shouldReceive('register')
         ->once()
         ->with(Mockery::on(fn($data) => $data['email'] === 'john@example.com'))
         ->andReturn(new User(['email' => 'john@example.com']));

    $this->app->instance(AuthService::class, $mock);

    $response = $this->post('/register', [
        'name' => 'John',
        'email' => 'john@example.com',
        'password' => 'secret123',
        'password_confirmation' => 'secret123',
    ]);

    $response->assertRedirect('/home');
}
}
