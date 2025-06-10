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

        $response = $this->post('/register', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'secret123'
        ]);

        $response->assertNoContent();

        $this->assertDatabaseHas('users', [
            'email' => 'jane@example.com'
        ]);

        Mail::assertSent(WelcomeMail::class);
    }
}
