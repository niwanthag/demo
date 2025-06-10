<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\UserService;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

class UserServiceTest extends TestCase
{
    use RefreshDatabase;

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

    public function test_register_method_with_mocked_mail_facade()
    {
        // This test demonstrates mocking Laravel facades
        Mail::fake();

        $service = new UserService();

        $userData = [
            'name' => 'Demo User',
            'email' => 'demo@example.com',
            'password' => 'password123'
        ];

        // Call the method
        $user = $service->register($userData);

        // Assert user was created
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('demo@example.com', $user->email);
        $this->assertEquals('Demo User', $user->name);

        // Assert that Mail facade was called correctly
        Mail::assertSent(WelcomeMail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });

        // Assert that exactly one mail was sent
        Mail::assertSentCount(1);
    }
}

