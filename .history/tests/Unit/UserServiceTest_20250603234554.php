<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\UserService;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeMail;
use App\Models\User;
use Mockery;

class UserServiceTest extends TestCase
{
    public function test_registers_user_and_sends_mail()
    {
        Mail::fake();

        $service = new UserService();

        $userData = [
            'name' => 'John',
            'email' => 'john@example.com',
            'password' => 'secret123'
        ];

        $user = $service->register($userData);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('john@example.com', $user->email);

        Mail::assertSent(WelcomeMail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }

    public function test_registers_user_via_auth_service()
    {
        $mock = Mockery::mock(UserService::class);
        $mock->shouldReceive('register')
             ->once()
             ->with(Mockery::on(fn($data) => $data['email'] === 'john@example.com'))
             ->andReturn(new User(['email' => 'john@example.com']));

        $this->app->instance(UserService::class, $mock);

        $response = $this->withoutMiddleware()
            ->post('/register', [
                'name' => 'John1',
                'email' => 'john1@example.com',
                'password' => 'secret1234',
                'password_confirmation' => 'secret1234',
            ]);

            dd($response);

        $response->assertRedirect('/home');
    }

}

