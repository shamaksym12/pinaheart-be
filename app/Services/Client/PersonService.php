<?php

namespace App\Services\Client;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\CoreService;
use App\Repositories\UserRepository;
use App\Repositories\ProfileRepository;
use App\User;
use App\UserSearch;
use App\Param;
use App\Activity;
use App\AdminActivityLog;
use App\Http\Resources\Client\Person\ListItem as PersonListItemResourse;
use App\Http\Resources\Client\Person\SearchShort as PersonSearchShortResourse;
use App\Http\Resources\Client\Person\SearchDetail as PersonSearchDetailResourse;
use App\Http\Resources\Client\Person\ListPaginateCollection;
use App\Http\Resources\Client\Person\ShortProfile as PersonShortProfileResourse;
use App\Http\Resources\Client\Person\DetailProfile as PersonDetailProfileResourse;
use App\Http\Resources\Client\Activity\Inbox as ActivityInboxResourse;
use App\Http\Resources\Client\Activity\FavoriteUser as ActivityFavoriteUserResourse;
use App\Http\Resources\Client\Activity\InterestUser as ActivityInterestUserResourse;
use App\Http\Resources\Client\Activity\BlockUser as ActivityBlockUserResourse;
use Illuminate\Support\Facades\Validator;
use App\Events\Person\ViewedShortProfile;
use App\Events\Person\ChangeFavorites;
use App\Events\Person\ChangeInterest;
use App\Events\Person\ChangeBlock;
use App\Events\Message\Logout;

class PersonService extends CoreService
{
    protected $userRepository;
    protected $profileRepository;
    protected $profileService;

    public function __construct(
        UserRepository $userRepository,
        ProfileRepository $profileRepository,
        ProfileService $profileService
    )
    {
        $this->userRepository = $userRepository;
        $this->profileRepository = $profileRepository;
        $this->profileService = $profileService;
    }

    public function getSearchParams(Request $request)
    {
        $me = auth()->user();
        $params = $this->profileRepository->getAllSearchParamsWithValue();
        foreach ($params as $param) {
            if($param->alias == 'relationship_youre_looking_for') {
                $searchParams[$param->alias] = $param->values->filter(function($item){
                    return $item->name !== 'Any';
                });
            } else {
                $searchParams[$param->alias] = $param->values;
            }
        }
        $searchParams['iam'] = $me->sex;
        $staticData['search_params'] = $searchParams;
        $staticData['location_params'] = $this->profileService->getLocationParams($me);

        return response()->result($staticData);
    }

    public function list(Request $request)
    {
         
        $me = auth()->user();
        $search = $this->getSearchFromRequest($request);
        if(($name = $request->name) && count($search)) {
            $dataSearch = [
                'data' => $search,
            ];
            $this->userRepository->saveSearch($me, $name, $dataSearch);
        }
        $items = User::query();
        if ( ! ($me->isAdmin() || $me->isManager())) {
            $items = $items->withOutBlocked();
        }
        $items = $items->search($search)->forList($me);
        return response()->result(new ListPaginateCollection($items->paginate(User::FRONT_PAGINATE_PER_PAGE)));
    }

    public function smartSearch(Request $request)
    {
        $me = auth()->user()->load(['match']);
        $search = [
            'sort' => 'last_active',
        ];
        if($name = $request->name) {
            if(is_numeric($name)) {
                $search['profile_id'] = $name;
            } else {
                $search['first_name'] = $name;
                $search['sex'] = optional($me->match)->sex;
            }
        }
        $items =  User::search($search)->forList($me)->paginate(User::FRONT_PAGINATE_PER_PAGE);

        return response()->result(new ListPaginateCollection($items));
    }

    /**START SEARCH */
    public function getSearchFromRequest(Request $request)
    {
        if($searcData = $request->search) {
            $params = $this->profileRepository->getAllSearchParams();
            $searcData['params'] = array_filter(array_only($searcData, $params->pluck('alias')->toArray()));
            $rules = $this->getSearchRules($searcData);
            if($place = array_get($searcData, 'place')) {
                $searcData['lat'] = array_get($place, 'geometry.location.lat');
                $searcData['long'] = array_get($place, 'geometry.location.lng');
            }
            $searcData['age_from'] = ! is_null(array_get($searcData, 'age_from')) ? (int) array_get($searcData, 'age_from') : null;
            $searcData['age_to'] = ! is_null(array_get($searcData, 'age_to')) ? (int) array_get($searcData, 'age_to') : null;
            $validator = Validator::make($searcData, $rules);
            $data = $validator->validated();
            return $data;
        }
        return [];
    }

    public function getSearchRules($data)
    {
        $age_from = array_get($data, 'age_from');
        $rules = [
            'iam' => 'nullable|in:M,F',
            'sex' => 'nullable|in:M,F',
            'age_from' => 'nullable|integer|gte:18|lte:80',
            'age_to' => 'nullable|integer|lte:80'.($age_from ?'|gte:age_from':''),
            'country_id' => 'nullable|integer',
            'lat' => 'nullable|numeric',
            'long' => 'nullable|numeric|required_with:lat',
            'formatted_address' => 'nullable|string',
            'distance' => 'nullable|integer',
            'distance_unit' => 'nullable|in:kms,miles',
            'with_photo' => 'nullable|boolean',
            'params' => 'nullable|array',
            'last_active' => 'nullable|in:week,2week,month,3month,6month,year',
            'sort' => 'nullable|in:last_active,newest,youngest',
            'only_online' => 'nullable|boolean',
            'admin_blocked' => 'boolean',
            'with_admin_comments' => 'boolean',
        ];
        return $rules;
    }
    /**END SEARCH */

    public function getShortProfile(User $user, Request $request)
    {
        
        $me = auth()->user();
        $item = $this->profileRepository->loadShortProfile($user, $me);
        $handleProfileParams = [];
        $shortProfileParams = Param::shortProfileParams()->get();
        $userProfileParams = $item->profileParams;
        foreach($shortProfileParams as $param) {
            $handleProfileParams[$param->alias] = $userProfileParams->getProfileParamValue($param);
        }

        event(new ViewedShortProfile($me, $user));

        $result = (new PersonShortProfileResourse($item))->withCustomData(compact('handleProfileParams'));
        return response()->result($result);
    }

    public function getDetailProfile(User $user, Request $request)
    {

        
        $me = auth()->user();
        $item = $this->profileRepository->loadDetailProfile($user, $me);
        $handleProfileParams = [];
        $allProfileParams = Param::profile()->get();
        $userProfileParams = $item->profileParams;
        foreach($allProfileParams as $param) {
            $handleProfileParams[$param->alias] = $userProfileParams->getProfileParamValue($param);
        }

        event(new ViewedShortProfile($me, $user));

        $result = (new PersonDetailProfileResourse($item))->withCustomData(compact('handleProfileParams'));
        return response()->result($result);
    }

    public function toggleFavorite(User $user, Request $request)
    {
        $me = auth()->user()->load(['favoritesUsers']);
        if($me->favoritesUsers->contains('id', $user->id)) {
            $me->favoritesUsers()->detach($user->id);
            $message = 'User removed from favorites';
            $added = false;
        } else {
            $me->favoritesUsers()->attach($user->id);
            $message = 'User added to favorites';
            $added = true;
        }

        event(new ChangeFavorites($me, $user, $added));

        return response()->result(true, $message);
    }

    public function toggleInterests(User $user, Request $request)
    {
        $me = auth()->user()->load(['interestedUsers']);
        if($me->interestedUsers->contains('id', $user->id)) {
            $me->interestedUsers()->detach($user->id);
            $message = 'User removed from interest';
            $added = false;
        } else {
            $me->interestedUsers()->attach($user->id);
            $message = 'User added to interest';
            $added = true;
        }

        event(new ChangeInterest($me, $user, $added));

        return response()->result(true, $message);
    }

    public function toggleBlock(User $user, Request $request)
    {
        $me = auth()->user()->load(['blockedUsers']);
        if($me->blockedUsers->contains('id', $user->id)) {
            $me->blockedUsers()->detach($user->id);
            $message = 'User unblocked';
            $added = false;
        } else {
            $me->blockedUsers()->attach($user->id);
            $message = 'User blocked';
            $added = true;
        }

        event(new ChangeBlock($me, $user, $added));

        return response()->result(true, $message);
    }

    public function updateComment(User $user, Request $request)
    {
        $me = auth()->user();
        $user->comment = $request->comment;
        $user->save();
        $message = 'Comment updated';

        AdminActivityLog::create([
            'staff_id' => $me->id,
            'action' => AdminActivityLog::ACTION_COMMENTED,
            'target_id' => $user->id,
        ]);

        return response()->result(true, $message);
    }

    public function updateAdminBlock(User $user, Request $request)
    {
        $me = auth()->user();
        $user->is_admin_block = $request->toggle;
        $user->save();
        $message = 'Member ' . ($request->toggle ? 'blocked' : 'unblocked');

        AdminActivityLog::create([
            'staff_id' => $me->id,
            'action' => $request->toggle ? AdminActivityLog::ACTION_BLOCKED : AdminActivityLog::ACTION_UNBLOCKED,
            'target_id' => $user->id,
        ]);
        
        if ($request->toggle) {
            event(new Logout($user));
        }

        return response()->result(true, $message);
    }

    /**Start match */
    public function getMatches(Request $request)
    {
        
        $me = auth()->user()->load(['match', 'matchParams']);

        $meMatch = $me->match;
        $dataSearch = $meMatch ? $meMatch->toArray() : [];
        $dataSearch['iam'] = $me->sex;
        //if empty match age

        if($meMatch && empty($meMatch->age_from) && empty($meMatch->age_to) && ($meAge = $me->age)) {
            if($me->isMale()) {
                $dataSearch['age_from'] = $meAge - round($meAge * 0.4);
                $dataSearch['age_to'] = $meAge;
            } else {
                $dataSearch['age_from'] = $meAge - 1;
                $dataSearch['age_to'] = round($meAge * 1.5);
            }
        }
        $params = [];
        foreach($me->matchParams as $param) {
            switch ($param->type_match) {
                case Param::TYPE_FIXED:
                    $params[$param->alias] = $param->pivot->value;
                    break;
                case Param::TYPE_MANY:
                    $params[$param->alias][] = $param->pivot->value_id;
                    break;
            }                
        }

        if ( ! empty($params['do_you_smoke']) && in_array(49, $params['do_you_smoke'])) { // if do smoke then occasionally smoke is logical
            $params['do_you_smoke'][] = 51;
        }

        if ( ! empty($params['do_you_drink']) && in_array(46, $params['do_you_drink'])) { // if do drink then occasionally drink is logical
            $params['do_you_drink'][] = 48;
        }  

        $dataSearch['params'] = $params;
        $dataSearch['sort'] = $request->get('sort', 'last_active');
                
        $items = User::query();
        $items = $items->search($dataSearch)->forList($me)->whereHas('photos', function($q) {
            $q->where('approved', true);
        });
        if ( ! ($me->isAdmin() || $me->isManager())) {
            $items = $items->withOutBlocked();
        }
        return response()->result(new ListPaginateCollection($items->paginate(User::FRONT_PAGINATE_PER_PAGE)));
    }
    /**End match */


    /**Start Searches */
    public function getSearchesLists(Request $request)
    {
        $me = auth()->user();

        return response()->result(PersonSearchShortResourse::collection($me->searches));
    }

    public function getSearch(UserSearch $userSearch, Request $request)
    {
        $me = auth()->user();
        customThrowIf($userSearch->user_id <> $me->id, 'Wrong search');

        return response()->result(new PersonSearchDetailResourse($userSearch));
    }

    public function deleteSearch(UserSearch $userSearch, Request $request)
    {
        $me = auth()->user();
        customThrowIf($userSearch->user_id <> $me->id, 'Wrong search');
        $userSearch->delete();

        return response()->result(true, 'Search deleted');
    }

    public function getSearchPeoples(UserSearch $userSearch, Request $request)
    {
        $me = auth()->user();
        customThrowIf($userSearch->user_id <> $me->id, 'Wrong search');

        $items = User::search($userSearch->data)->forList($me)->paginate(User::FRONT_PAGINATE_PER_PAGE);

        return response()->result(new ListPaginateCollection($items));
    }
    /**End Searches */

    /**Start Activities */
    public function getInboxActivities(Request $request)
    {
        $me = auth()->user();
        $result = collect([]);
        $admin_ids = User::whereIn('role', ['admin', 'manager', 'junior'])->get()->pluck('id')->toArray();
 
        if($type = $request->get('type', 'all')) {  
            switch ($type) {
                case 'all':
                    $items = $me->inboxActivityUsers()->whereNotIn('activities.type', [Activity::TYPE_ADD_TO_BLOCK, Activity::TYPE_REMOVE_FROM_BLOCK])->with(['mainPhoto'])->whereNotIn('who_id', $admin_ids)->orderBy('activities.updated_at', 'desc')->get();
                    $result = ActivityInboxResourse::collection($items);
                    break;
                case Activity::TYPE_VIEW:
                    $items = $me->inboxActivityUsers()->with(['mainPhoto'])->wherePivot('type', Activity::TYPE_VIEW)->whereNotIn('who_id', $admin_ids)->orderBy('activities.updated_at', 'desc')->get();
                    $result = ActivityInboxResourse::collection($items);
                    break;
                case 'interest':
                    $items = $me->interestedByUsers()->with(['mainPhoto'])->whereNotIn('who_id',  $admin_ids)->orderBy('user_interest_users.updated_at', 'desc')->get();
                    $result = ActivityInterestUserResourse::collection($items);
                    break;
                case 'favorites':
                    $items = $me->favoritedByUsers()->with(['mainPhoto'])->whereNotIn('who_id',  $admin_ids)->orderBy('user_favorites.updated_at', 'desc')->get();
                    $result = ActivityFavoriteUserResourse::collection($items);
                    break;
            }
        }

        return response()->result($result);
    }

    public function getSentActivities(Request $request)
    {
        $me = auth()->user();
        $result = collect([]);
        if($type = $request->get('type', 'all')) {
            switch ($type) {
                case 'all':
                    $items = $me->sentActivityUsers()->with(['mainPhoto'])->orderBy('activities.updated_at', 'desc')->get();
                    $result = ActivityInboxResourse::collection($items);
                    break;
                case Activity::TYPE_VIEW:
                    $items = $me->sentActivityUsers()->with(['mainPhoto'])->wherePivot('type', Activity::TYPE_VIEW)->orderBy('activities.updated_at', 'desc')->get();
                    $result = ActivityInboxResourse::collection($items);
                    break;
                case 'interest':
                    $items = $me->interestedUsers()->with(['mainPhoto'])->orderBy('user_interest_users.updated_at', 'desc')->get();
                    $result = ActivityInterestUserResourse::collection($items);
                    break;
                case 'favorites':
                    $items = $me->favoritesUsers()->with(['mainPhoto'])->orderBy('user_favorites.updated_at', 'desc')->get();
                    $result = ActivityFavoriteUserResourse::collection($items);
                    break;
                case 'blocked':
                    $items = $me->blockedUsers()->with(['mainPhoto'])->orderBy('user_blocked_users.updated_at', 'desc')->get();
                    $result = ActivityBlockUserResourse::collection($items);
                    break;
            }
        }

        return response()->result($result);
    }
    /**End Activities */

}
