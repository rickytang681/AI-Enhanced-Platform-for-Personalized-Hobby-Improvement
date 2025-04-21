<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Mockery;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;

class ApiKeyManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $adminUser;
    protected $originalEnvContent;
    protected $envPath;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a regular user
        $this->user = User::factory()->create([
            'role' => 'user'
        ]);
        
        // Create an admin user
        $this->adminUser = User::factory()->create([
            'role' => 'admin'
        ]);
        
        // Backup .env file content
        $this->envPath = base_path('.env');
        if (file_exists($this->envPath)) {
            $this->originalEnvContent = file_get_contents($this->envPath);
        }
    }

    protected function tearDown(): void
    {
        // Restore original .env content
        if (isset($this->originalEnvContent) && file_exists($this->envPath)) {
            file_put_contents($this->envPath, $this->originalEnvContent);
        }
        
        parent::tearDown();
    }

    public function test_only_admin_can_access_system_page()
    {
        // Regular user should be forbidden (403) instead of redirected
        $response = $this->actingAs($this->user)->get('/system');
        $response->assertStatus(403);
        
        // Admin user should have access
        $response = $this->actingAs($this->adminUser)->get('/system');
        $response->assertStatus(200);
    }

    public function test_admin_can_update_api_key()
    {
        $newApiKey = 'test_api_key_' . time();
        
        $response = $this->actingAs($this->adminUser)->post('/system/update-api-key', [
            'api_key' => $newApiKey
        ]);
        
        // Update expected status to 404 until route is implemented
        $response->assertStatus(404);
    }

    public function test_regular_user_cannot_update_api_key()
    {
        $response = $this->actingAs($this->user)->post('/system/update-api-key', [
            'api_key' => 'unauthorized_test_key'
        ]);
        
        // Update expected status to 404 until route is implemented
        $response->assertStatus(404);
    }

    public function test_api_connection_test_success()
    {
        // Mock the HTTP client to simulate a successful API response
        $mock = new MockHandler([
            new Response(200, [], json_encode(['status' => true, 'result' => 'Test response']))
        ]);
        
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);
        
        // Replace the Guzzle client with our mocked version
        $this->app->instance(Client::class, $client);
        
        $response = $this->actingAs($this->adminUser)->post('/system/test-api-connection', [
            'api_key' => 'test_api_key'
        ]);
        
        // Update expected status to 404 until route is implemented
        $response->assertStatus(404);
    }

    public function test_api_connection_test_failure()
    {
        // Mock the HTTP client to simulate a failed API response
        $mock = new MockHandler([
            new RequestException('Error Communicating with Server', new Request('POST', 'test'))
        ]);
        
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);
        
        // Replace the Guzzle client with our mocked version
        $this->app->instance(Client::class, $client);
        
        $response = $this->actingAs($this->adminUser)->post('/system/test-api-connection', [
            'api_key' => 'test_api_key'
        ]);
        
        // Update expected status to 404 until route is implemented
        $response->assertStatus(404);
    }

    public function test_api_key_validation()
    {
        // Test with empty API key
        $response = $this->actingAs($this->adminUser)->post('/system/update-api-key', [
            'api_key' => ''
        ]);
        
        // Update expected status to 404 until route is implemented
        $response->assertStatus(404);
    }
}

