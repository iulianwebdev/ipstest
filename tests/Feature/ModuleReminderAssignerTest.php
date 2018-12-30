<?php

namespace Tests\Feature;

use App\Http\Helpers\InfusionsoftHelper;
use App\Http\Managers\ModuleReminderManager;
use App\Http\Requests\AssignModuleRequest;
use App\Module;
use App\Tag;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Tests\Traits\HttpPost;
use \Mockery;

class ModuleReminderAssignerTest extends TestCase
{
    use HttpPost;
    
    public function setUp()
    {
        parent::setUp();
        $this->url = '/api/module_reminder_assigner';

        $this->user = $this->getFirstAvailableContact();
        $this->actingAs($this->user);
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
        Auth::logout();

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

    public function testEarlyFailureResponseForNoCoursesAvailable()
    {
        $testMock = Mockery::mock(InfusionsoftHelper::class);
        $testMock
            ->shouldReceive('getContact')
            ->with($this->user->email)->once()
            ->andReturn([
                'Email' => $this->user->email,
                'Id' => rand(),
            ]);
        $this->app->instance(InfusionsoftHelper::class, $testMock);

        $payload = [
            'contact_email' => $this->user->email
        ];

        $response = $this->httpPost($payload);

        $this->assertEquals(ModuleReminderManager::ALREADY_UP_TO_DATE_MESSAGE, $response->json()['message']);
    }


    /**
     *
     *   START TESTING ALL POSSIBLE SCENARIOS
     *
     */

    /**
     * Test the api with IPA IEA and no completed modules
     *
     * snake case for readability sake
     */
    public function testFor_2_courses_without_completed_modules()
    {
        $correctTag = Tag::where('name', 'Start IPA Module 1 Reminders')->first();

        $this->setUpMock($correctTag, 'ipa,iea');

        $payload = [
            'contact_email' => $this->user->email
        ];

        $response = $this->httpPost($payload);

        $response->assertStatus(self::OK);
    }

    /**
     * test with IPA, 2,3,4 (completed), and IEA with no completed modules
     */
    public function testFor_2_courses_with_completed_modules()
    {
        $this->user->completed_modules()->attach(Module::where('course_key', 'ipa')->limit(3)->offset(1)->get());

        $correctTag = Tag::where('name', 'Start IPA Module 5 Reminders')->first();

        $this->setUpMock($correctTag, 'ipa,iea');

        $payload = [
            'contact_email' => $this->user->email
        ];

        $response = $this->httpPost($payload);

        $this->cleanUp($this->user);
        $response->assertStatus(self::OK);
    }

    public function testFor_1_courses_with_last_module_completed()
    {
        $this->user->completed_modules()->attach(Module::where('name', 'IPA Module 7')->get());

        $correctTag = Tag::completed();

        $this->setUpMock($correctTag, 'ipa');

        $payload = [
            'contact_email' => $this->user->email
        ];

        $response = $this->httpPost($payload);

        $response->assertStatus(self::OK);
    }

    public function testFor_2_courses_first_one_with_last_module_completed()
    {
        $this->user->completed_modules()->attach(Module::where('name', 'IPA Module 7')->get());

        $correctTag = Tag::where('name', 'Start IEA Module 1 Reminders')->first();

        $this->setUpMock($correctTag, 'ipa,iea');

        $payload = [
            'contact_email' => $this->user->email
        ];

        $response = $this->httpPost($payload);

        $this->cleanUp($this->user);
        $response->assertStatus(self::OK);
    }

    /**
     *
     * Test that the completed tag is set if on both courses
     * the last  modules have been completed
     *
     */
    public function testFor_2_courses_both_with_last_module_completed()
    {
        $modulesToSetCompleted = Module::whereIn('name', ['IPA Module 7','IEA Module 7'])
            ->get();

        $this->user->completed_modules()->attach($modulesToSetCompleted);

        $correctTag = Tag::completed();

        $this->setUpMock($correctTag, 'ipa,iea');

        $payload = [
            'contact_email' => $this->user->email
        ];

        $response = $this->httpPost($payload);

        $response->assertStatus(self::OK);
    }


    /**
     *
     *  END OF TESTING SCENARIOS
     *
     *
     */


    private function cleanUp(User $user)
    {
        $this->user->completed_modules()->detach();
    }

    /**
     * Function to return a user
     *
     * idealy it should be a fake user instance
     * TODO: make fake factory functions for User model
     *
     * @return [type] [description]
     */
    private function getFirstAvailableContact()
    {
        // TODO: replace with faker instance
        return User::where('is_admin', 0)->limit(1)->get()->first();
    }

    /**
     * Set up the common scenario Mocks for testing the logic
     * @param Tag    $expectedTag
     * @param string $products
     */
    private function setUpMock(Tag $expectedTag, string $products)
    {
        $id = rand();

        $testMock = Mockery::mock(InfusionsoftHelper::class);
        $testMock
            ->shouldReceive('getContact')
            ->with($this->user->email)->once()
            ->andReturn([
                'Email' => $this->user->email,
                '_Products' => $products,
                'Id' => $id,
            ]);

        $testMock
            ->shouldReceive('addTag')
            ->with($id, $expectedTag->id)->once()
            ->andReturn(true);

        $this->app->instance(InfusionsoftHelper::class, $testMock);
    }

    public function tearDown()
    {
        if ($this->user) {
            $this->cleanUp($this->user);
        }

        parent::tearDown();
    }
}
