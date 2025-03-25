<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_protected_pages()
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');

        $response = $this->get('/profile');
        $response->assertRedirect('/login');
    }

    public function test_regular_user_cannot_access_admin_pages()
    {
        $user = User::factory()->create(['role' => 'user']);
        $this->actingAs($user);

        $response = $this->get('/admin/dashboard');
        $response->assertStatus(403);

        $response = $this->get('/admin/users');
        $response->assertStatus(403);
    }

    public function test_admin_can_access_admin_pages()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $response = $this->get('/admin/dashboard');
        $response->assertStatus(200);

        $response = $this->get('/admin/users');
        $response->assertStatus(200);
    }

    public function test_user_session_expires_correctly()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Simulate session expiration
        $this->travel(3)->hours();

        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_remember_me_functionality()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123')
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
            'remember' => 'on'
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertNotNull($user->fresh()->remember_token);
    }
}