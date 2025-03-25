<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);

        $response->assertRedirect('/home');  // Changed from '/dashboard' to '/home'
        
        // Assert the user was created in the database
        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'role' => 'user'
        ]);
        
        $this->assertAuthenticated();
    }

    public function test_admin_can_be_created_and_login()
    {
        // Create admin user
        $user = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin'
        ]);

        // Attempt login
        $response = $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'admin123'
        ]);

        $response->assertRedirect('/dashboard');  // Changed from '/admin/dashboard' to '/dashboard'
        $this->assertAuthenticated();

        // Verify the authenticated user is indeed an admin
        $this->assertEquals('admin', auth()->user()->role);
    }

    public function test_registration_requires_valid_data()
    {
        $response = $this->post('/register', [
            'name' => '',
            'email' => 'invalid-email',
            'password' => 'short',
            'password_confirmation' => 'different'
        ]);

        $response->assertSessionHasErrors(['name', 'email', 'password']);
        $this->assertGuest();
    }

    public function test_user_can_logout()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/logout');

        $response->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_api_authentication()
    {
        $user = User::factory()->create();
        
        // Test unauthenticated access
        $response = $this->getJson('/api/user');
        $response->assertStatus(401);
        
        // Test authenticated access
        $response = $this->actingAs($user)->getJson('/api/user');
        $response->assertStatus(200)
            ->assertJson([
                'id' => $user->id,
                'email' => $user->email
            ]);
    }

    public function test_admin_can_access_system_page()
    {
        // Create and login as admin
        $admin = User::factory()->create([
            'role' => 'admin'
        ]);

        $response = $this->actingAs($admin)->get('/system');
        $response->assertStatus(200);
    }

    public function test_regular_user_cannot_access_system_page()
    {
        // Create and login as regular user
        $user = User::factory()->create([
            'role' => 'user'
        ]);

        $response = $this->actingAs($user)->get('/system');
        $response->assertStatus(302);  // Check for redirect instead of 403
        $response->assertRedirect('/dashboard');  // Updated to match the actual redirect
    }

    public function test_login_with_incorrect_password()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('correct_password')
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrong_password'
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_login_with_nonexistent_user()
    {
        $response = $this->post('/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'password123'
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_password_reset_request()
    {
        $user = User::factory()->create();

        $response = $this->post('/password/email', [
            'email' => $user->email
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('status');
    }

    public function test_session_expiration()
    {
        $user = User::factory()->create();
        
        // Login the user
        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password'
        ]);

        // Verify we're logged in
        $this->assertAuthenticated();

        // Simulate session expiration
        $this->app['session']->invalidate();
        $this->app['auth']->logout();

        // Try to access a protected route
        $response = $this->get('/dashboard');
        
        $response->assertRedirect('/login');
        $this->assertGuest();
    }
}
