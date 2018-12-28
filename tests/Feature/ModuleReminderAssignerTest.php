<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Traits\HttpPost;
use Tests\TestCase;
use App\User;

class ModuleReminderAssignerTest extends TestCase
{
    use HttpPost;
    
    public function setUp() 
    {
        parent::setUp();
        $this->url = '/api/module_reminder_assigner';
    }

    /**
     * test that there is an end-point to the uri
     * 
     * @return void
     */
    public function testApiExists() 
    {
        
        $payload = [];
        $response = $this->httpPost($payload);
        $this->assertNotEquals(self::NOT_FOUND, $response->status());
    }


    /**
     * Test that un-authenticated users can use the api.
     * Could be adjusted to test un-authorized access also.
     *
     * @return void
     */
    public function testApiReturns403()
    {
        $payload = [];

        $response = $this->httpPost($payload);

        $response->assertStatus(self::UNAUTHORIZED);
    }

    /**
     * Check that a wrong format email sends the right json 
     * 
     * @return void
     */
    public function testApiDoesNotAcceptInvalidEmail() 
    {
        $this->actingAs(new User([
            'id' => 1,
            'name' => 'Test User'
        ]));

        $payload = [
            'contact_email' => 'not-.valid@inexistent_email',
        ];

        $response = $this->httpPost($payload);

        $response->assertExactJson([
            'success' => false,
            'message' => 'Email not valid.',
        ]);
        
    }
}
