<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeMail;
use App\Services\UserService;
use App\Models\User;
use Mockery;

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

    public function test_controller_calls_user_service_with_mocked_service()
    {
        // Create mock of UserService
        $mockUserService = Mockery::mock(UserService::class);

        // Set expectations - the service should be called once with the validated data
        $mockUserService->shouldReceive('register')
            ->once()
            ->with(Mockery::on(function ($data) {
                return $data['email'] === 'john@example.com'
                    && $data['name'] === 'John Doe'
                    && isset($data['password']);
            }))
            ->andReturn(new User(['email' => 'john@example.com', 'name' => 'John Doe']));

        // Replace the real service with our mock in the service container
        $this->app->instance(UserService::class, $mockUserService);

        // Make the request
        $response = $this->withoutMiddleware()
            ->post('/register', [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => 'secret123',
                'password_confirmation' => 'secret123',
            ]);

        // Assert the response
        $response->assertRedirect('/home');

        // The mock expectations will be automatically verified by Mockery
    }
}
