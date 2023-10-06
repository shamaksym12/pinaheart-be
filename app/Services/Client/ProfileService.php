<?php

namespace App\Services\Client;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Services\CoreService;
use App\Repositories\UserRepository;
use App\Repositories\ProfileRepository;
use App\Services\StorageService;
use App\User;
use App\Photo;
use App\Param;
use App\Country;
use App\UserNotifySetting;
use App\Http\Resources\Client\User\Short as UserShortResourse;
use App\Http\Resources\Client\User\Main as UserMainResourse;
use App\Http\Resources\Client\User\Photo as UserPhotoResourse;
use App\Http\Resources\Client\Profile\UserWithParams as ProfileUserWithParamsResourse;
use App\Http\Resources\Client\Profile\UserMatchesWithParams as ProfileUserMatchesWithParamsResourse;
use App\Http\Resources\Client\Profile\Interest as ProfileInterestResourse;
use App\Http\Resources\Client\Profile\Personality as ProfilePersonalityResourse;
use App\Http\Resources\Client\Profile\NotifySetting as ProfileNotifySettingResourse;
use App\Http\Resources\Client\Person\Info as PersonInfoResourse;
use App\Events\User\SetOff as SetOffEvent;

class ProfileService extends CoreService
{
    protected $userRepository;
    protected $profileRepository;
    protected $storageService;

    public function __construct(
        UserRepository $userRepository,
        ProfileRepository $profileRepository,
        StorageService $storageService
    )
    {
        $this->userRepository = $userRepository;
        $this->profileRepository = $profileRepository;
        $this->storageService = $storageService;
    }

    public function getUser(Request $request)
    {
        $me = auth()->user()->load(['mainPhoto', 'info', 'match', 'coupons' => function($q) {
            $q->where('coupon_user.expired_at', '>', now());
        }])->loadCount(['inboxUnreadMessages']);
        

        $countOnlineMembers = $this->userRepository->getCountOnlineMembers();
        $result = (new UserMainResourse($me))->addData(compact('countOnlineMembers'));

        return response()->result($result);
    }

    public function setMyEmail(Request $request)
    {
        $me = auth()->user();
        $me = $this->userRepository->update($me, [
            'email' => $request->email,
        ]);

        return response()->result(new UserShortResourse($me), 'Your email is successfully changed. Please, login');
    }

    public function setMyPassword(Request $request)
    {
        $me = auth()->user();
        if ( ! $me->isSocialUser()) {
            $check = Hash::check($request->old_password, $me->password);
            customThrowIf( ! $check, 'Your current password is incorrect');
        }
        $me = $this->userRepository->update($me, ['password' => bcrypt($request->password)]);

        return response()->result(new UserShortResourse($me), 'Password changed successfully');
    }

    public function getMyInfo(Request $request)
    {
        $me = auth()->user()->load('info');

        return response()->result(new PersonInfoResourse($me->info));
    }

    public function setMyInfo(Request $request)
    {
        $me = auth()->user();
        $dataInfo = $request->validated();
        $me = $this->userRepository->saveInfo($me, $dataInfo);
        $me->load('info');

        return response()->result(new PersonInfoResourse($me->info));
    }

    public function setOff(Request $request)
    {
        $me = auth()->user();
        customThrowIf($me->isOff(), 'Your account already disabled');
        if ( ! $me->isSocialUser()) {
            $check = Hash::check($request->password, $me->password);
            customThrowIf( ! $check, 'Your current password is incorrect');
        }
        $dataUser = [
            'is_off' => true,
        ];
        $me = $this->userRepository->update($me, $dataUser);
        $dataOff = [
            'off_at' => now(),
            'reason' => $request->reason,
        ];
        $me = $this->userRepository->saveOff($me, $dataOff);

        event(new SetOffEvent($me, $request->reason));

        return response()->result(true, 'Your account disabled');
    }

    public function setOn(Request $request)
    {
        $me = auth()->user();
        customThrowIf( ! $me->isOff(), 'Your account already enabled');

        $dataUser = [
            'is_off' => false,
        ];
        $me = $this->userRepository->update($me, $dataUser);
        $dataOff = [
            'on_at' => now(),
        ];
        $me = $this->userRepository->saveOff($me, $dataOff);

        return response()->result(true, 'Your account enabled');
    }

    public function toggleBusy(Request $request)
    {
        $me = auth()->user();
        customThrowIf( ! $me->isPaid(), 'Your account not platinum');
        $newValue = ! $me->is_busy;
        $me = $this->userRepository->update($me, [
            'is_busy' => $newValue,
        ]);

        return response()->result(true, 'Your account set as '.($newValue ? 'busy' : 'online'));
    }

    public function toggleHidden(Request $request)
    {
        $me = auth()->user();
        customThrowIf( ! $me->isPaid(), 'Your account not platinum');
        $newValue = ! $me->is_hidden;
        $me = $this->userRepository->update($me, [
            'is_hidden' => $newValue,
        ]);

        return response()->result(true, 'Your account set as '.($newValue ? 'hidden' : 'visible'));
    }

    public function getNotifySettings(Request $request)
    {
        $me = auth()->user();
        $items = $me->notifySettings()->get();

        return response()->result(ProfileNotifySettingResourse::collection($items));
    }

    public function setNotifySettings(Request $request)
    {
        $me = auth()->user();
        $data = $request->validated();
        $dataSettings = array_get($data, 'settings');
        // dd($data);
        foreach($dataSettings as $setting) {
            $this->userRepository->saveNotifySetting($me, $setting);
        }
        // dd($me->notifySettings);

        return response()->result(ProfileNotifySettingResourse::collection($me->notifySettings));
    }

    /**START PHOTOS */
    public function getPhotos(Request $request)
    {
        $me = auth()->user()->load(['photos' => function($q){
            $q->orderBy('is_main','desc')->orderBy('created_at', 'asc');
        }]);

        return response()->result(UserPhotoResourse::collection($me->photos));
    }

    public function addPhoto(Request $request)
    {
        $me = auth()->user()->loadCount('photos');
        customThrowIf($me->photos_count >= Photo::USER_PHOTO_LIMIT, 'Limit for photos is '.Photo::USER_PHOTO_LIMIT);

        $dataPhoto = $this->storageService->storeUserPhoto($request->photo);
        $dataPhoto['is_main'] = ($me->photos_count == 0);
        $photo = $this->userRepository->createUserPhoto($me, $dataPhoto);

        return response()->result(new UserPhotoResourse($photo), 'Photo added');
    }

    public function setMainPhoto(Photo $photo, $request)
    {
        $me = auth()->user()->load('photos');
        customThrowIf( ! $me->photos->contains('id', $photo->id), 'Wrong photo');

        $photo = $this->userRepository->setMainPhoto($me, $photo);

        return response()->result(new UserPhotoResourse($photo));
    }

    public function deletePhoto(Photo $photo, $request)
    {
        $me = auth()->user()->load('photos');
        customThrowIf( ! $me->photos->contains('id', $photo->id), 'Wrong photo');

        $this->storageService->deleteUserPhoto($photo);
        $photo->delete();

        return response()->result(true, 'Photo deleted');
    }

    public function loadFacebookPhotos(Request $request)
    {
        $photos = $request->all();
        if($countPhotos = count($photos)) {
            $me = auth()->user()->loadCount('photos');
            customThrowIf(($me->photos_count + $countPhotos) >= Photo::USER_PHOTO_LIMIT, 'Limit for photos is '.Photo::USER_PHOTO_LIMIT);
            $k = 0;
            foreach ($photos as $photo) {
                $source = array_get($photo, 'source');
                if($source && str_start($source, 'http')) {
                    $dataPhoto = $this->storageService->loadAnotherAvatar($source);
                    $dataPhoto['is_main'] = ($me->photos_count == 0);
                    $photo = $this->userRepository->createUserPhoto($me, $dataPhoto);
                    $k++;
                }
            }
            return response()->result(true, 'Loaded '.$k.' '.str_plural('photo', $k));
        }
    }
    /**END PHOTOS */

    /**START LOCATIONS */
    public function getСountries(Request $request)
    {
        $countries = Country::get();
        return response()->result($countries);
    }
    /**END LOCATIONS */

    /**START PROFILE */
    public function getAllProfileParams(Request $request)
    {
        $me = auth()->user()->load(['location']);
        $params = $this->profileRepository->getAllProfileParamsWithValue();
        $staticData= [];
        $profileParams = [];
        foreach ($params as $param) {
            $profileParams[$param->alias] = $param->values;
        }
        $staticData['location_params'] = $this->getLocationParams($me);
        $staticData['profile_params'] = $profileParams;

        return response()->result($staticData);
    }

    public function getProfile(Request $request)
    {
        $me = auth()->user()->load(['profileParams', 'location']);
        $me->load(['info', 'location']);
        $profileParams = $this->getProfileParams($me);
        $result = (new ProfileUserWithParamsResourse($me))->withCustomData(compact('profileParams'));

        return response()->result($result);
    }

    public function setProfileParams(Request $request)
    {
        $me = auth()->user();
        $validated = $request->validated();
        foreach($validated as $field => $value) {
            if(in_array($field, ['first_name', 'sex'])) {
                $dataUser = [
                    $field => $value,
                ];
                $me = $this->userRepository->update($me, $dataUser);
                return response()->result(true);
            } elseif(in_array($field, ['country_id', 'place'])) {
                switch ($field) {
                    case 'country_id':
                        $dataLocation = [
                            'country_id' => $value,
                            'formatted_address' => null,
                            'place_id' => null,
                            'lat' => null,
                            'long' => null,
                        ];
                        break;
                    case 'place':
                        $dataLocation = [
                            'formatted_address' => array_get($value, 'formatted_address'),
                            'place_id' => array_get($value, 'place_id'),
                            'lat' => array_get($value, 'geometry.location.lat'),
                            'long' => array_get($value, 'geometry.location.lng'),
                        ];
                        break;
                }
                $me = $this->userRepository->saveLocation($me, $dataLocation);
                return response()->result(true);
            } elseif(in_array($field, ['dob_day', 'dob_month', 'dob_year'])) {
                $dob = $me->dob ?? (now()->subYears($me->age ?? 18));
                switch ($field) {
                    case 'dob_day':
                        $dob->setDay($value);
                        break;
                    case 'dob_month':
                        $dob->setMonth($value);
                        break;
                    case 'dob_year':
                        $dob->setYear($value);
                        break;
                }
                $me = $this->userRepository->update($me, compact('dob'));
                return response()->result(true);
            } elseif(in_array($field, ['heading', 'about', 'looking'])){
                $dataInfo = [
                    $field => $value,
                ];
                $me = $this->userRepository->saveInfo($me, $dataInfo);
                return response()->result(true);
            } elseif($param = $this->profileRepository->findParamByAlias($field)) {
                switch ($param->type) {
                    case Param::TYPE_ONE:
                        $pivotData[$param->id] = ['value_id' => $value];
                        $me->profileParams()->syncWithoutDetaching($pivotData);
                        break;
                    case Param::TYPE_FIXED:
                        $pivotData[$param->id] = ['value' => (string)$value];
                        $me->profileParams()->syncWithoutDetaching($pivotData);
                        break;
                    case Param::TYPE_MANY:
                        if(is_array($value) && count($value)) {
                            $me->profileParams()->detach($param->id);
                            foreach ($value as $valueId) {
                                $me->profileParams()->attach([$param->id => ['value_id' => $valueId]]);
                            }
                        }
                        break;
                }
                return response()->result(true);
            }
        }
        return response()->result(false);
    }

    public function getInfo()
    {

    }

    public function setInfo()
    {
        // in_array($field, ['heading', 'about', 'looking'])){
        // $dataInfo = [
        //     $field => $value,
        // ];
        // $me = $this->userRepository->saveInfo($me, $dataInfo);
    }

    /**START MATCH */
    public function getAllMatchParams(Request $request)
    {
        $me = auth()->user();
        $params = $this->profileRepository->getAllMatchParamsWithValue();
        $staticData= [];
        $matchParams = [];
        foreach ($params as $param) {
            $matchParams[$param->alias] = $param->values;
        }
        $staticData['match_params'] = $matchParams;
        $staticData['location_params'] = $this->getLocationParams($me);

        return response()->result($staticData);
    }

    public function getMatches(Request $request)
    {
        $me = auth()->user()->load(['match', 'matchParams']);
        $matchParams = $this->getMatchParams($me);
        $locationParams = $this->getLocationParams($me);

        $result = (new ProfileUserMatchesWithParamsResourse($me->match))->withCustomData(compact('matchParams'));
        return response()->result($result);
    }

    public function setAllMatchParams(Request $request)
    {
        $me = auth()->user();        
        $dataMatch = array_except($request->all(), 'match_params');        
        if ( ! empty($request->age_from)) {
            $dataMatch['age_from'] = $request->age_from == 0 ? null : $request->age_from;
        }

        if ( ! empty($request->age_to)) {
            $dataMatch['age_to'] = $request->age_to == 0 ? null : $request->age_to;
        }

        if($place = $request->place){
            $dataMatch['formatted_address'] = array_get($place, 'formatted_address');
            $dataMatch['place_id'] = array_get($place, 'place_id');
            $dataMatch['lat'] = array_get($place, 'geometry.location.lat');
            $dataMatch['long'] = array_get($place, 'geometry.location.lng');
        }
        elseif( ! array_get($dataMatch, 'formatted_address')) {
            $dataMatch['formatted_address'] = null;
            $dataMatch['place_id'] = null;
            $dataMatch['lat'] = null;
            $dataMatch['long'] = null;
        };
        $me = $this->userRepository->saveMatch($me, $dataMatch);

        if($matchParams = $request->match_params) {
            $matchDelete = array_filter($matchParams, function($item) {
                return is_null($item);
            });

            $matchParams = array_filter($matchParams, function($item){
                return ! is_null($item);
            });
            
            $pivotData = [];
            $allMatchParams = $this->profileRepository->getAllMatchParams();

            foreach($matchDelete as $alias => $value)
            {
                if($param = $allMatchParams->firstWhere('alias', $alias)) {
                    $me->matchParams()->detach($param->id);
                }
            }
            
            foreach($matchParams as $alias => $value)
            {
                if($param = $allMatchParams->firstWhere('alias', $alias)) { 
                    switch ($param->type_match) {
                        case Param::TYPE_FIXED:
                            $pivotData[$param->id] = ['value' => ($value ? (string)$value : null)];
                            break;
                        case Param::TYPE_MANY:
                            if(is_array($value)) {
                                $me->matchParams()->detach($param->id);
                                if(count($value)) {
                                    foreach ($value as $valueId) {
                                        $me->matchParams()->attach([$param->id => ['value_id' => $valueId]]);
                                    }
                                }
                            }                            
                            if (is_string($value)) {
                                $me->matchParams()->detach($param->id);
                                $me->matchParams()->attach([$param->id => ['value_id' => $value]]);
                            }
                            break;
                    }
                }
            }

            $me->matchParams()->syncWithoutDetaching($pivotData);
        }        
    }

    public function setMatches(Request $request)
    {       
        $me = auth()->user();
        $dataMatch = array_except($request->validated(), 'match_params');
        $dataMatch['age_from'] = $request->age_from == 0 ? null : $request->age_from;
        $dataMatch['age_to'] = $request->age_to == 0 ? null : $request->age_to;
        if($place = $request->place){
            $dataMatch['formatted_address'] = array_get($place, 'formatted_address');
            $dataMatch['place_id'] = array_get($place, 'place_id');
            $dataMatch['lat'] = array_get($place, 'geometry.location.lat');
            $dataMatch['long'] = array_get($place, 'geometry.location.lng');
        }
        elseif( ! array_get($dataMatch, 'formatted_address')) {
            $dataMatch['formatted_address'] = null;
            $dataMatch['place_id'] = null;
            $dataMatch['lat'] = null;
            $dataMatch['long'] = null;
        };
        $me = $this->userRepository->saveMatch($me, $dataMatch);
        if($matchParams = $request->match_params) {
            $matchParams = array_filter($matchParams, function($item){
                return ! is_null($item);
            });
            $pivotData = [];
            $allMatchParams = $this->profileRepository->getAllMatchParams();
            foreach($matchParams as $alias => $value)
            {
                if($param = $allMatchParams->firstWhere('alias', $alias)) {
                    switch ($param->type_match) {
                        case Param::TYPE_FIXED:
                            $pivotData[$param->id] = ['value' => ($value ? (string)$value : null)];
                            break;
                        case Param::TYPE_MANY:
                            if(is_array($value)) {
                                $me->matchParams()->detach($param->id);
                                if(count($value)) {
                                    foreach ($value as $valueId) {
                                        $me->matchParams()->attach([$param->id => ['value_id' => $valueId]]);
                                    }
                                }
                            }
                            break;
                    }
                }
            }
            $me->matchParams()->syncWithoutDetaching($pivotData);
        }
        $matchParams = $this->getMatchParams($me);
        $me->load(['match']);

        $result = (new ProfileUserMatchesWithParamsResourse($me->match))->withCustomData(compact('matchParams'));
        return response()->result($result, 'Your Matches has been updated');
    }

    public function setDefaultUserMatch(User $user)
    {
        $dataMatch = $user->getDefaultMatch();
        $this->userRepository->saveMatch($user, $dataMatch);
    }

    public function setDefaultProfileParams(User $user)
    {
        //Relationship you’re looking for:
        $param = $this->profileRepository->findParamByAlias('relationship_youre_looking_for');
        $value = $this->profileRepository->findParamValueByName($param, 'Any');
        $user->profileParams()->attach([$param->id => ['value_id' => $value->id]]);
    }

    public function setDefaultNotifySetting(User $user)
    {
        $dataSetting = [
            'type' => UserNotifySetting::TYPE_EMAIL,
            'name' => UserNotifySetting::NAME_NEW_MESSAGE,
            'value' => UserNotifySetting::VALUE_DAILY,
        ];
        $this->userRepository->saveNotifySetting($user, $dataSetting);
        $dataSetting = [
            'type' => UserNotifySetting::TYPE_EMAIL,
            'name' => UserNotifySetting::NAME_NEW_ACTIVITY,
            'value' => UserNotifySetting::VALUE_DAILY,
        ];
        $this->userRepository->saveNotifySetting($user, $dataSetting);
    }
    /**END MATCH */

    /**START INTEREST */
    public function getInterest(Request $request)
    {
        $me = auth()->user();
        $me->load('interest');

        return response()->result(new ProfileInterestResourse($me->interest));
    }

    public function setInterest(Request $request)
    {
        $me = auth()->user();
        $dataInterest = $request->validated();
        $me = $this->userRepository->saveInterest($me, $dataInterest);
        $me->load('interest');

        return response()->result(new ProfileInterestResourse($me->interest), 'Your hobbies & interests have been updated');
    }
    /**END INTEREST */

    /**START PERSONALITY */
    public function getPersonality(Request $request)
    {
        $me = auth()->user();
        $me->load('personality');

        return response()->result(new ProfilePersonalityResourse($me->personality));
    }

    public function setPersonality(Request $request)
    {
        $me = auth()->user();
        $dataPersonality = $request->validated();
        $me = $this->userRepository->savePersonality($me, $dataPersonality);
        $me->load('personality');

        return response()->result(new ProfilePersonalityResourse($me->personality), 'Your Personality Profile has been updated');
    }
    /**END PERSONALITY */

    protected function getProfileParams(User $user)
    {
        $user->loadMissing('profileParams');
        $profileParams = [];
        foreach($user->profileParams as $param) {
            switch ($param->type) {
                case Param::TYPE_ONE:
                    $profileParams[$param->alias] = $param->pivot->value_id;
                    break;
                case Param::TYPE_FIXED:
                    $profileParams[$param->alias] = $param->pivot->value;
                    break;
                case Param::TYPE_MANY:
                    $profileParams[$param->alias][] = $param->pivot->value_id;
                    break;
            }
        }
        return collect($profileParams);
    }

    public function getLocationParams(User $user)
    {
        $params = [
            'countries' => Country::customOrder('code', ['US', 'PH'], 'desc')->get(),
        ];
        return $params;
    }

    protected function getMatchParams(User $user)
    {
        $user->loadMissing('matchParams');
        $allMatchParams = $this->profileRepository->getAllMatchParams();
        $userMatchParams = $user->matchParams;
        $matchParams = [];
        foreach($allMatchParams as $param) {
            $exists = $userMatchParams->where('id', $param->id);
            if($exists->count()) {
                switch ($param->type_match) {
                    case Param::TYPE_FIXED:
                        $matchParams[$param->alias] = $exists->first()->pivot->value;
                        break;
                    case Param::TYPE_MANY:
                        $matchParams[$param->alias] = $exists->pluck('pivot.value_id');
                        break;
                }
            } else {
                $matchParams[$param->alias] = null;
            }
        }
        return collect($matchParams);
    }
    /**END PROFILE */

    public function checkUnfinishRegister($user) {
        $user->loadMissing('profileParams');
        $profileParams = [];
        foreach($user->profileParams as $param) {
            switch ($param->type) {
                case Param::TYPE_ONE:
                    $profileParams[$param->alias] = $param->pivot->value_id;
                    break;
                case Param::TYPE_FIXED:
                    $profileParams[$param->alias] = $param->pivot->value;
                    break;
                case Param::TYPE_MANY:
                    $profileParams[$param->alias][] = $param->pivot->value_id;
                    break;
            }
        }
        
        return ['finish' => count($profileParams) >= 10];
    }
}
