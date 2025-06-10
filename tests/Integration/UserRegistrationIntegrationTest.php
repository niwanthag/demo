<?php
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use App\Services\UserService;
use App\Mail\WelcomeMail;

class UserRegistrationIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_service_creates_user_and_triggers_mail()
    {
        Mail::fake();

        $service = new UserService();

        $user = $service->register([
            'name' => 'Alice',
            'email' => 'alice@example.com',
            'password' => 'secret123',
        ]);

        $this->assertDatabaseHas('users', ['email' => 'alice@example.com']);

        Mail::assertSent(WelcomeMail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }
}
