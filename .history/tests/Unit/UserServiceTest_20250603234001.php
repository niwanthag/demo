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

}

