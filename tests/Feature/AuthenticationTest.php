<?php

namespace Tests\Feature;

use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    /**
     * Test login
     *
     * @return void
     */
    protected $user;

    public function setUpOnce(): void
    {
        $this->artisan('migrate:fresh');
    }

    public function test_login()
    {
        $user = \App\Models\User::factory()->create();
        $response = $this->post('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password'
        ]);
        $this->assertAuthenticatedAs($user);
    }

    public function test_output_login()
    {
        $user = \App\Models\User::factory()->create();
        $response = $this->post('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password'
        ]);

        $response->assertJsonStructure([
            'user',
            'token'
        ]);
    }

    public function test_login_invalid()
    {
        $response = $this->post('/api/auth/login', [
            'email' => 'anything@xyz.com',
            'password' => 'wrongpass'
        ]);
        
        $response->assertJson(function (AssertableJson $json){
            $json->has('message');
        });
    }

    public function test_logout()
    {
        $user = \App\Models\User::factory()->create();
        $login = $this->post('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password'
        ]);

        $token = $login->json()['token'];

        $logout = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])
        ->post('/api/auth/logout')
        ->assertJson([
            "message" => "Logout succed"
        ]);
    }

    public function test_logout_without_authenticate()
    {
        $logout = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('/api/auth/logout')
        ->assertJson([
            "message" => "Unauthenticated."
        ]);
    }
}
