<?php

namespace App\Services\Admin;

use Illuminate\Http\Request;
use App\Services\CoreService;
use App\Services\StorageService;
use App\Repositories\UserRepository;
use App\Repositories\ProfileRepository;
use App\User;
use App\Param;
use App\Photo;
use App\AdminActivityLog;
use App\Http\Resources\Admin\User\UserPaginateCollection;
use App\Http\Resources\Admin\User\DetailProfile as DetailProfileResource;
use App\Http\Resources\Admin\User\UserPhotosPaginateCollection;
use App\Http\Resources\Admin\User\Photo as UserPhotoResourse;
use App\Events\Message\Logout;
use App\Events\User\AdminUpdated;

class UserService extends CoreService
{
    protected $storageService;
    protected $userRepository;
    protected $profileRepository;

    public function __construct(
        StorageService $storageService,
        UserRepository $userRepository,
        ProfileRepository $profileRepository
    )
    {
        $this->storageService = $storageService;
        $this->userRepository = $userRepository;
        $this->profileRepository = $profileRepository;
    }

    public function updateEmail(User $user, Request $request) {
        $user->email = $request->email;
        $user->save();
        
        $me = auth()->user();

        AdminActivityLog::create([
            'staff_id' => $me->id,
            'action' => AdminActivityLog::ACTION_CHANGE_EMAIL,
            'target_id' => $user->id,
        ]);

        event(new AdminUpdated($user));
    }

    public function updatePassword(User $user, Request $request) {
        $user->password = bcrypt($request->password);
        $user->save();

        $me = auth()->user();
        AdminActivityLog::create([
            'staff_id' => $me->id,
            'action' => AdminActivityLog::ACTION_CHANGE_PASS,
            'target_id' => $user->id,
        ]);

        event(new AdminUpdated($user));
    }

    public function list(Request $request)
    {
        $me = auth()->user();
        $filter = $request->get('filter', 'all');
        $search = $request->get('query');

        $query = User::where('users.id', '<>',$me->id)->with([
            'adminData',            
            'coupons' => function($q){
                $q->where('coupon_user.expired_at', '>', now());
            },
        ]);
        
        switch ($filter) {
            case 'all':
                $query->orderBy('created_at', 'desc');
                break;
            case 'blocked';
                $query->where('status', User::STATUS_BLOCKED);
                $query->leftJoin('user_admin_data', 'users.id', '=', 'user_admin_data.user_id');
                $query->select('users.*', 'user_admin_data.blocked_at');
                $query->orderBy('blocked_at', 'desc');
                break;
            case 'withcomment';
                $query->leftJoin('user_admin_data', 'users.id', '=', 'user_admin_data.user_id');
                $query->select('users.*', 'user_admin_data.comment', 'user_admin_data.comented_at');
                $query->whereNotNull('comment');
                $query->orderBy('comented_at', 'desc');
                break;
        }

        if ( ! empty($search)) {
            $query = $query->where('first_name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%')
                    ->orWhere('profile_id', 'like', '%' . $search . '%');
        }

        $items = $query->paginate(User::ADMIN_PAGINATE_PER_PAGE);

        return response()->result(new UserPaginateCollection($items));
    }

    public function get(User $user, Request $request)
    {
        $me = auth()->user();
        customThrowIf($me->id == $user->id, 'Wrong user');
        $user->load(['photos', 'location.country', 'match', 'info', 'interest', 'personality', 'profileParams.values', 'adminData', 'coupons' => function($q){
            $q->where('coupon_user.expired_at', '>', now());
        },]);
        $handleProfileParams = [];
        $allProfileParams = Param::profile()->get();
        $userProfileParams = $user->profileParams;
        foreach($allProfileParams as $param) {
            $handleProfileParams[$param->alias] = $userProfileParams->getProfileParamValue($param);
        }

        $result = (new DetailProfileResource($user))->withCustomData(compact('handleProfileParams'));
        return response()->result($result);
    }

    public function toggleUserBlock(User $user, Request $request)
    {
        if($user->status == User::STATUS_BLOCKED) {
            $newStatus = User::STATUS_ACTIVE;
            $message = 'User activated';
            $dataAdmin = [
                'blocked_at' => null,
            ];
        } else {
            $newStatus = User::STATUS_BLOCKED;
            $message = 'User blocked';
            $dataAdmin = [
                'blocked_at' => now(),
            ];            
        }

        $me = auth()->user();

        AdminActivityLog::create([
            'staff_id' => $me->id,
            'action' => $user->status != User::STATUS_BLOCKED ? AdminActivityLog::ACTION_BLOCKED : AdminActivityLog::ACTION_UNBLOCKED,
            'target_id' => $user->id,
        ]);
        
        if ($user->status != User::STATUS_BLOCKED) {
            event(new Logout($user));
        }

        $this->userRepository->update($user, ['status' => $newStatus, 'is_admin_block' => $user->status != User::STATUS_BLOCKED ? true: false]);
        $this->userRepository->saveAdminData($user, $dataAdmin);

        return response()->result(true, $message);
    }

    public function setComment(User $user, Request $request)
    {
        $user->comment = $request->text;
        $user->save();
        
        $dataAdmin = [
            'comment' => $request->text,
            'comented_at' => now(),
        ];
        $this->userRepository->saveAdminData($user, $dataAdmin);
        
        $me = auth()->user();
        AdminActivityLog::create([
            'staff_id' => $me->id,
            'action' => AdminActivityLog::ACTION_COMMENTED,
            'target_id' => $user->id,
        ]);

        return response()->result(true, 'Comment saved');
    }

    /**Start photos */
    public function listWithPhotos(Request $request)
    {
        $me = auth()->user();
        $latestPhoto = Photo::select('user_id',\DB::raw('max(created_at) as last_photo_created_at'))
                   ->whereNull('approved')->groupBy('user_id');
        $items =  User::where('users.id', '<>',$me->id)
            ->leftJoinSub($latestPhoto, 'photos', function ($join) {
                $join->on('users.id', '=', 'photos.user_id');
            })
            ->select('users.*', 'photos.last_photo_created_at')
            ->orderBy('last_photo_created_at', 'desc')
            ->with(['photos' => function($q){
                $q->whereNull('approved');
                $q->orderBy('is_main', 'desc');
                $q->orderBy('created_at', 'desc');
            }])
            ->whereHas('photos', function($q){
                $q->whereNull('approved');
            })
            ->paginate(User::ADMIN_PAGINATE_PER_PAGE);

        return response()->result(new UserPhotosPaginateCollection($items));
    }

    public function approvePhoto(Photo $photo, Request $request)
    {
        customThrowIf($photo->beenApproved(), 'Photo been approved');
        $photoData = [
            'approved' => true,
            'verified_at' => now(),
        ];
        $existsAprovedMain = $photo->user->photos()->approved()->where('is_main', true)->exists();
        if( ! $existsAprovedMain) {
            $this->userRepository->setMainPhoto($photo->user, $photo);
            $message = 'Photo approved and set main';
        } else {
            $message = 'Photo approved';
        }
        $photo = $this->userRepository->updatePhoto($photo, $photoData);

        event(new AdminUpdated($photo->user));

        return response()->result(new UserPhotoResourse($photo), $message);
    }

    public function disapprovePhoto(Photo $photo, Request $request)
    {
        customThrowIf($photo->beenApproved(), 'Photo been approved');
        $photoData = [
            'approved' => false,
            'verified_at' => now(),
        ];
        $message = 'Photo disapproved';
        $photo = $this->userRepository->updatePhoto($photo, $photoData);

        return response()->result(new UserPhotoResourse($photo), $message);
    }
    /**End photos */
}
