<?php

namespace Tests\Unit\Controllers;

use App\Http\Controllers\ProfileController;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    protected $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new ProfileController();
    }

    public function test_profile_method_returns_correct_view()
    {
        // Create a unique user for this test
        $user = User::factory()->create([
            'email' => 'test1_' . Str::random(5) . '@example.com',
        ]);
        
        $this->actingAs($user);
        
        $response = $this->controller->profile();
        
        $this->assertEquals('profile', $response->getName());
        $this->assertArrayHasKey('user', $response->getData());
        $this->assertEquals($user->id, $response->getData()['user']->id);
    }

    public function test_edit_profile_method_returns_correct_view()
    {
        // Create a unique user for this test
        $user = User::factory()->create([
            'email' => 'test2_' . Str::random(5) . '@example.com',
        ]);
        
        $this->actingAs($user);
        
        $response = $this->controller->editProfile();
        
        $this->assertEquals('profile', $response->getName());
        $this->assertArrayHasKey('user', $response->getData());
        $this->assertEquals($user->id, $response->getData()['user']->id);
    }

    public function test_update_method_validates_input()
    {
        // Create a unique user for this test
        $user = User::factory()->create([
            'email' => 'test3_' . Str::random(5) . '@example.com',
        ]);
        
        $this->actingAs($user);
        
        $request = new Request([
            'username' => '',  // Invalid: empty username
            'email' => 'not-an-email',  // Invalid: not an email
            'password' => '123',  // Invalid: too short
            'password_confirmation' => '1234'  // Invalid: doesn't match
        ]);
        
        $this->expectException(\Illuminate\Validation\ValidationException::class);
        $this->controller->update($request);
    }

    public function test_update_method_handles_password_change()
    {
        Storage::fake('public');
        
        // Create a unique user for this test
        $user = User::factory()->create([
            'email' => 'test4_' . Str::random(5) . '@example.com',
            'password' => Hash::make('password123')
        ]);
        
        $this->actingAs($user);
        
        $request = Request::create('/profile', 'POST', [
            'username' => 'Updated Name',
            'email' => 'updated_' . Str::random(5) . '@example.com',
            'phone' => '9876543210',
            'current_password' => 'password123',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123'
        ]);
        
        $response = $this->controller->update($request);
        
        $user->refresh();
        $this->assertEquals('Updated Name', $user->name);
        $this->assertTrue(Hash::check('newpassword123', $user->password));
    }

    public function test_update_method_handles_profile_picture_upload()
    {
        Storage::fake('public');
        
        // Create a unique user for this test
        $user = User::factory()->create([
            'email' => 'test5_' . Str::random(5) . '@example.com',
        ]);
        
        $this->actingAs($user);
        
        $file = UploadedFile::fake()->image('avatar.jpg');
        
        $request = Request::create('/profile', 'POST', [
            'username' => $user->name,
            'email' => $user->email,
        ], [], [
            'profile_picture' => $file
        ]);
        
        $response = $this->controller->update($request);
        
        $user->refresh();
        $this->assertNotNull($user->profile_picture);
        Storage::disk('public')->assertExists($user->profile_picture);
    }

    public function test_update_method_rejects_incorrect_current_password()
    {
        // Create a unique user for this test
        $user = User::factory()->create([
            'email' => 'test6_' . Str::random(5) . '@example.com',
            'password' => Hash::make('correct-password')
        ]);
        
        $this->actingAs($user);
        
        $request = Request::create('/profile', 'POST', [
            'username' => $user->name,
            'email' => $user->email,
            'current_password' => 'wrong-password',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123'
        ]);
        
        $response = $this->controller->update($request);
        
        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
        $this->assertEquals(session('errors')->get('current_password')[0] ?? null, 'The current password is incorrect.');
    }
}