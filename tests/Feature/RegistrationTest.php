<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RegistrationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_it_creates_user_on_registration() {
        $response = $this->post('api/register', [
            'name' => 'Example',
            'email' => 'example@test.com',
            'userName' => 'e.g.69',
            'password' => 'secret',
            'password_confirmation' => 'secret',
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'success' => true,
        ]);
        $this->assertDatabaseHas('users', [
            'email' => 'example@test.com',
        ]);
    }

    public function test_it_creates_user_account_on_registration() {
        $response = $this->post('api/register', [
            'name' => 'Example',
            'email' => 'example@test.com',
            'userName' => 'e.g.69',
            'password' => 'secret',
            'password_confirmation' => 'secret',
        ]);
        
        $user = User::where('email', 'example@test.com')->first();

        $this->assertNotNull($user->account);
    }

    public function test_user_name_validation() {
        $notValidResponse = $this->post('api/register', [
            'name' => 'Example',
            'email' => 'example@test.com',
            'userName' => 'e(g%69',
            'password' => 'secret',
            'password_confirmation' => 'secret',
        ]);

        $validResponse = $this->post('api/register', [
            'name' => 'Example',
            'email' => 'example@test.com',
            'userName' => '_e.g-69',
            'password' => 'secret',
            'password_confirmation' => 'secret',
        ]);

        $notValidResponse->assertJsonFragment([
            'success' => false,
        ]);
        $validResponse->assertJsonFragment([
            'success' => true,
        ]);
    }
}
