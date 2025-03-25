<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ProfileManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test user with a unique email
        $this->user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test.user.' . Str::random(10) . '@example.com',
            'phone' => '123456789',
            'password' => Hash::make('password123')
        ]);
    }

    public function test_user_can_update_profile_details()
    {
        $newEmail = 'updated.' . Str::random(10) . '@example.com';
        
        $response = $this->actingAs($this->user)->post('/profile', [
            'username' => 'Updated Name',
            'email' => $newEmail,
            'phone' => '987654321'
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'name' => 'Updated Name',
            'email' => $newEmail,
            'phone' => '987654321'
        ]);
    }

    public function test_user_can_update_password()
    {
        $response = $this->actingAs($this->user)->post('/profile', [
            'username' => $this->user->name,
            'email' => $this->user->email,
            'current_password' => 'password123',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123'
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->user->refresh();
        $this->assertTrue(Hash::check('newpassword123', $this->user->password));
    }

    public function test_user_can_update_profile_picture()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('profile.jpg');

        $response = $this->actingAs($this->user)->post('/profile', [
            'username' => $this->user->name,
            'email' => $this->user->email,
            'profile_picture' => $file
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        // Assert the file was stored
        Storage::disk('public')->assertExists('profile-pictures/' . $file->hashName());
        
        // Assert the user's profile picture was updated in the database
        $this->user->refresh();
        $this->assertEquals('profile-pictures/' . $file->hashName(), $this->user->profile_picture);
    }

    public function test_invalid_email_format_is_rejected()
    {
        $response = $this->actingAs($this->user)->post('/profile', [
            'email' => 'invalid-email-format'
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_duplicate_email_is_rejected()
    {
        // Create another user
        $anotherUser = User::factory()->create([
            'email' => 'another.' . Str::random(10) . '@example.com'
        ]);

        $response = $this->actingAs($this->user)->post('/profile', [
            'email' => $anotherUser->email
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_username_is_required()
    {
        $response = $this->actingAs($this->user)->post('/profile', [
            'username' => '',
            'email' => $this->user->email
        ]);

        $response->assertSessionHasErrors('username');
    }

    public function test_invalid_profile_picture_format_is_rejected()
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->create('document.pdf', 1000);

        $response = $this->actingAs($this->user)->post('/profile', [
            'profile_picture' => $file
        ]);

        $response->assertSessionHasErrors('profile_picture');
    }

    public function test_large_profile_picture_is_rejected()
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->image('large-profile.jpg')->size(21000);

        $response = $this->actingAs($this->user)->post('/profile', [
            'profile_picture' => $file
        ]);

        $response->assertSessionHasErrors('profile_picture');
    }

    public function test_user_can_view_profile_page()
    {
        $response = $this->actingAs($this->user)->get('/profile');

        $response->assertStatus(200);
        $response->assertViewIs('profile');
    }

    public function test_guest_cannot_access_profile_page()
    {
        $response = $this->get('/profile');
        $response->assertRedirect('/login');
    }

    protected function tearDown(): void
    {
        // Clean up storage after each test
        Storage::fake('public')->deleteDirectory('profile-pictures');
        parent::tearDown();
    }
}


