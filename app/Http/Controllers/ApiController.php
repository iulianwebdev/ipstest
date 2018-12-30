<?php

namespace App\Http\Controllers;

use App\Http\Helpers\InfusionsoftHelper;
use App\Http\Managers\ModuleReminderManager;
use App\Http\Requests\AssignModuleRequest;
use Illuminate\Support\Facades\Request;
use Response;

class ApiController extends Controller
{
    private $manager;

    public function __construct(ModuleReminderManager $manager) 
    {
        
        $this->manager = $manager;
    }
    
    // Todo: Module reminder assigner
    
    public function assingModuleReminder(AssignModuleRequest $request)
    {
        $validated = $request->validated();

        $responseMessage = $this->manager->attachNextReminderTagOrFail($validated['contact_email']);

        return Response::json([
            'success' => true,
            'message' => $responseMessage,
        ], 200);
    }

    private function exampleCustomer()
    {
        $infusionsoft = new InfusionsoftHelper();

        $uniqid = uniqid();

        $infusionsoft->createContact([
            'Email' => $uniqid.'@test.com',
            '_Products' => 'ipa,iea'
        ]);

        $user = User::create([
            'name' => 'Test ' . $uniqid,
            'email' => $uniqid.'@test.com',
            'password' => bcrypt($uniqid)
        ]);

        // attach IPA M1-3 & M5
        $user->completed_modules()->attach(Module::where('course_key', 'ipa')->limit(3)->get());
        $user->completed_modules()->attach(Module::where('name', 'IPA Module 5')->first());


        return $user;
    }
}
