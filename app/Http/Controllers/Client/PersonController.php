<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Client\PersonService;
use App\Http\Requests\Client\Person\GetList as PersonGetListRequest;
use App\UserSearch;
use App\User;

class PersonController extends Controller
{
    protected $personService;

    public function __construct(PersonService $personService)
    {
        $this->personService = $personService;
    }

    public function getSearchParams(Request $request)
    {
        return $this->personService->getSearchParams($request);
    }

    public function list(PersonGetListRequest $request)
    {
        return $this->personService->list($request);
    }

    public function smartSearch(PersonGetListRequest $request)
    {
        return $this->personService->smartSearch($request);
    }

    public function getMatches(Request $request)
    {
        return $this->personService->getMatches($request);
    }

    public function getShortProfile(User $user, Request $request)
    {
        return $this->personService->getShortProfile($user, $request);
    }

    public function getDetailProfile(User $user, Request $request)
    {
        return $this->personService->getDetailProfile($user, $request);
    }

    public function toggleFavorite(User $user, Request $request)
    {
        return $this->personService->toggleFavorite($user, $request);
    }

    public function toggleInterests(User $user, Request $request)
    {
        return $this->personService->toggleInterests($user, $request);
    }

    public function toggleBlock(User $user, Request $request)
    {
        return $this->personService->toggleBlock($user, $request);
    }

    public function updateComment(User $user, Request $request) {
        return $this->personService->updateComment($user, $request);
    }

    public function updateAdminBlock(User $user, Request $request) {
        return $this->personService->updateAdminBlock($user, $request);
    }

    /**Start Searches */
    public function getSearchesLists(Request $request)
    {
        return $this->personService->getSearchesLists($request);
    }

    public function getSearch(UserSearch $userSearch, Request $request)
    {
        return $this->personService->getSearch($userSearch ,$request);
    }

    public function deleteSearch(UserSearch $userSearch, Request $request)
    {
        return $this->personService->deleteSearch($userSearch ,$request);
    }

    public function getSearchPeoples(UserSearch $userSearch, Request $request)
    {
        return $this->personService->getSearchPeoples($userSearch ,$request);
    }
    /**End Searches */

    /**Start Activities */
    public function getInboxActivities(Request $request)
    {
        return $this->personService->getInboxActivities($request);
    }

    public function getSentActivities(Request $request)
    {
        return $this->personService->getSentActivities($request);
    }
    /**End Activities */


}
