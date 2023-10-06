<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Client\ProfileService;
use App\Photo;
use App\Http\Requests\Client\Profile\AddPhoto as ProfileAddPhotoRequest;
use App\Http\Requests\Client\Profile\SetProfileParams as ProfileSetProfileParams;
use App\Http\Requests\Client\Profile\SetMatches as ProfileSetMatches;
use App\Http\Requests\Client\Profile\SetInterest as ProfileSetInterest;
use App\Http\Requests\Client\Profile\SetPersonality as ProfileSetPersonality;
use App\Http\Requests\Client\Profile\SetEmail as ProfileSetEmail;
use App\Http\Requests\Client\Profile\SetPassword as ProfileSetPassword;
use App\Http\Requests\Client\Profile\SetInfo as ProfileSetInfo;
use App\Http\Requests\Client\Profile\SetOff as ProfileSetOff;
use App\Http\Requests\Client\Profile\SetNotifySettings as ProfileSetNotifySettings;
use App\Services\Client\PersonService;

class ProfileController extends Controller
{
    protected $profileService;
    protected $personService;

    public function __construct(ProfileService $profileService, PersonService $personService)
    {
        $this->profileService = $profileService;
        $this->personService = $personService;
    }

    public function getUser(Request $request)
    {
        return $this->profileService->getUser($request);
    }

    public function getMyDetailProfile(Request $request)
    {
        $me = auth()->user();
        return $this->personService->getDetailProfile($me, $request);
    }

    public function setMyEmail(ProfileSetEmail $request)
    {
        return $this->profileService->setMyEmail($request);
    }

    public function setMyPassword(ProfileSetPassword $request)
    {
        return $this->profileService->setMyPassword($request);
    }

    public function getMyInfo(Request $request)
    {
        return $this->profileService->getMyInfo($request);
    }

    public function setMyInfo(ProfileSetInfo $request)
    {
        return $this->profileService->setMyInfo($request);
    }

    public function setOff(ProfileSetOff $request)
    {
        return $this->profileService->setOff($request);
    }

    public function setOn(Request $request)
    {
        return $this->profileService->setOn($request);
    }

    public function toggleBusy(Request $request)
    {
        return $this->profileService->toggleBusy($request);
    }

    public function toggleHidden(Request $request)
    {
        return $this->profileService->toggleHidden($request);
    }

    public function getNotifySettings(Request $request)
    {
        return $this->profileService->getNotifySettings($request);
    }

    public function setNotifySettings(ProfileSetNotifySettings $request)
    {
        return $this->profileService->setNotifySettings($request);
    }

    /**START PHOTOS */
    public function getPhotos(Request $request)
    {
        return $this->profileService->getPhotos($request);
    }

    public function addPhoto(ProfileAddPhotoRequest $request)
    {
        return $this->profileService->addPhoto($request);
    }

    public function setMainPhoto(Photo $photo, Request $request)
    {
        return $this->profileService->setMainPhoto($photo, $request);
    }

    public function deletePhoto(Photo $photo, Request $request)
    {
        return $this->profileService->deletePhoto($photo, $request);
    }

    public function loadFacebookPhotos(Request $request)
    {
        return $this->profileService->loadFacebookPhotos($request);
    }
    /**END PHOTOS */

    /**START LOCATIONS */
    public function getСountries(Request $request)
    {
        return $this->profileService->getСountries($request);
    }
    /**END LOCATIONS */

    /**START PROFILE */
    public function getAllProfileParams(Request $request)
    {
        return $this->profileService->getAllProfileParams($request);
    }

    public function getProfile(Request $request)
    {
        return $this->profileService->getProfile($request);
    }

    public function setProfileParams(ProfileSetProfileParams $request)
    {
        return $this->profileService->setProfileParams($request);
    }
    /**END PROFILE */

    /**START MATCH */
    public function getAllMatchParams(Request $request)
    {
        return $this->profileService->getAllMatchParams($request);
    }

    public function setAllMatchParams(Request $request)
    {
        return $this->profileService->setAllMatchParams($request);
    }

    public function getMatches(Request $request)
    {
        return $this->profileService->getMatches($request);
    }

    public function setMatches(ProfileSetMatches $request)
    {
        return $this->profileService->setMatches($request);
    }
    /**END MATCH */

    /**START INTEREST */
    public function getInterest(Request $request)
    {
        return $this->profileService->getInterest($request);
    }

    public function setInterest(ProfileSetInterest $request)
    {
        return $this->profileService->setInterest($request);
    }
    /**END INTEREST */

    /**START PERSONALITY */
    public function getPersonality(Request $request)
    {
        return $this->profileService->getPersonality($request);
    }

    public function setPersonality(ProfileSetPersonality $request)
    {
        return $this->profileService->setPersonality($request);
    }
    /**END PERSONALITY */

    public function checkUnfinishRegister(Request $request) {
        $user = auth()->user();
        return $this->profileService->checkUnfinishRegister($user);
    }
}
