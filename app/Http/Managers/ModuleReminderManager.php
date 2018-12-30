<?php

namespace App\Http\Managers;

use App\Collections\ModuleCollection;
use App\Entities\Contact;
use App\Http\Helpers\InfusionsoftHelper;
use App\Http\Requests\AssignModuleRequest;
use App\Module;
use App\Tag;
use App\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;

class ModuleReminderManager
{
    const USER_NOT_FOUND_MESSAGE = 'User could not be found.';
    const ALREADY_UP_TO_DATE_MESSAGE = 'Contact up to date.';
    const CONTACT_NOT_FOUND_MESSAGE = 'Contact could not be found.';
    const TAG_NOT_UPDATED = 'Error when trying to set reminder tag.';
    const SUCCESS = 'Reminder set succesfully.';


    private $infHelper;

    public function __construct(InfusionsoftHelper $infHelper)
    {
        // TODO: inject by contract not concrete implementation
        $this->infHelper = $infHelper;
    }
    
    public function attachNextReminderTagOrFail(string $email): string
    {
        $user = $this->getUserWithContact($email);

        if ($user->contact->hasCompletedAllModulesTag() || $user->contact->hasNoCoursesAssigned()) {
            return self::ALREADY_UP_TO_DATE_MESSAGE;
        }
        

        // TODO: check if the user with this email has any completed modules
        $this->addTagIfNeeded($user);

        return self::SUCCESS; // ~_~
    }

    /**
     * Get the user with a Contact object
     * to use later on 
     * 
     * @param  string $email 
     * @return User        
     */
    public function getUserWithContact(string $email): User
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            AssignModuleRequest::jsonError(self::USER_NOT_FOUND_MESSAGE);
        }

        $contact = $this->infHelper->getContact($user->email);

        if (!$contact) {
            AssignModuleRequest::jsonError(self::CONTACT_NOT_FOUND_MESSAGE);
        }

        $user->contact = new Contact($contact);

        return $user;
    }

    private function addTagIfNeeded(User $user)
    {
        $tag = $this->getCorrectTag($user);

        $response = $this->infHelper->addTag($user->contact->id, $tag->id);

        if (empty($response)) {
            AssignModuleRequest::jsonError(self::TAG_NOT_UPDATED);
        }
    }

    private function getCorrectTag(User $user): Tag
    {
        $userCourses = $user->contact->products;

        $availableModules = $user
                            ->availableModules($userCourses)
                            ->get()
                            ->groupBy('course_key')
                            ->sortBy(function ($item, $key) use ($userCourses) {
                                return array_search($key, $userCourses);
                            });

        try {
            $nextModuleReminder = $this->selectReminderModule($availableModules);
        } catch (ModelNotFoundException $e) {
            return Tag::completed();
        }

        return Tag::forModule($nextModuleReminder);
    }

    /**
     * Function that handles the logic of selecting the right module
     * that needs a reminder tag
     *
     * Returns Module instance or throws an exception if it can't find any
     * meaning all modules have been completed
     * 
     * @param  ModuleCollection $availableModules 
     * @return Module
     * @throws ModelNotFoundException                             
     */
    private function selectReminderModule(ModuleCollection $availableModules): Module
    {
        foreach ($availableModules as $course => $modules) {
            if (!$modules->hasLast()) {
                continue;
            }

            $selectedModule = $modules->lastModule();

            // while nextLabel is not an empty
            while ($nextLabel = $modules->getNextAvailableLabel($selectedModule->name)) {
                $selectedModule = $modules->nextAvailable($nextLabel);
            }
            return $selectedModule;
        }
        // if no module found throw Exception
        throw new ModelNotFoundException();
    }
}
