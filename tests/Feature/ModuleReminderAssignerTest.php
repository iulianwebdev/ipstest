<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Traits\HttpPost;
use Tests\TestCase;

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
}
