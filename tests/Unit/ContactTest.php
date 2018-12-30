<?php

namespace Tests\Unit;

use App\Entities\Contact;
use App\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ContactTest extends TestCase
{
    private $ALL_MODULE_COMPLETED_TAG_ID;

    public function setUp()
    {
        parent::setUp();
        $this->ALL_MODULE_COMPLETED_TAG_ID = Tag::completed()->id;
        # code...
    }
    /**
     * test that instantiation throws exception on missing id key
     *
     */
    public function testInstantiationThrowsAnException()
    {
        $contactData = [
            'Email' => 'test@test.com',
            '_Products' => 'ipa',
            'Groups' => '110'
        ];

        $this->expectException(\Exception::class);

        $contact = new Contact($contactData);
    }

    public function test_hasCompetedAllModules_methodReturnsFalse()
    {
        $contactData = [
            'Email' => 'test@test.com',
            '_Products' => 'ipa',
            'Id' => uniqid(),
            'Groups' => '110'
        ];

        $contact = new Contact($contactData);

        $this->assertFalse($contact->hasCompletedAllModulesTag());
    }

    public function test_hasCompetedAllModules_returnsTrue()
    {
        $contactData = [
            'Email' => 'test@test.com',
            'Id' => uniqid(),
            '_Products' => 'ipa',
            'Groups' => '110,'.$this->ALL_MODULE_COMPLETED_TAG_ID
        ];

        $contact = new Contact($contactData);

        $this->assertTrue($contact->hasCompletedAllModulesTag());
    }

    public function test_hasNoCoursesAssigned_returnsTrue()
    {
        $contactData = [
            'Email' => 'test@test.com',
            'Id' => uniqid(),
            '_Products' => '',
            'Groups' => '110,'.$this->ALL_MODULE_COMPLETED_TAG_ID
        ];

        $contact = new Contact($contactData);

        $this->assertTrue($contact->hasNoCoursesAssigned());
    }

    /** @test */
    public function test_firstModule_returnsCorrectCourse()
    {
        $contactData = [
            'Email' => 'test@test.com',
            'Id' => uniqid(),
            '_Products' => 'ipa,iaa',
            'Groups' => '110,'.$this->ALL_MODULE_COMPLETED_TAG_ID
        ];

        $contact = new Contact($contactData);

        $this->assertEquals('ipa', $contact->firstModuleKey());
    }
}
