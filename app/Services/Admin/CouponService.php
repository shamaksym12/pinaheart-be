<?php

namespace App\Services\Admin;

use App\Coupon;
use Illuminate\Http\Request;
use App\Services\CoreService;
use App\Repositories\CouponRepository;
use App\Http\Resources\Admin\Coupon\CouponPaginate as CouponPaginateResourse;
use App\Http\Resources\Admin\Coupon\CouponWithUsers as CouponWithUsersResourse;
use App\Http\Resources\Admin\Coupon\CouponPaginateCollection;
use App\Events\User\BecomePaid as UserBecomePaidEvent;
use App\Events\User\BecomeFree as UserBecomeFreeEvent;

class CouponService extends CoreService
{
    protected $couponRepository;

    public function __construct(CouponRepository $couponRepository)
    {
        $this->couponRepository = $couponRepository;
    }

    public function list(Request $request)
    {
        $items = Coupon::withCount(['users'])->customOrder('status', [Coupon::STATUS_ACTIVE, Coupon::STATUS_PAUSED, Coupon::STATUS_DELETED], 'asc')->latest('updated_at')->paginate(Coupon::ADMIN_PAGINATE_PER_PAGE);

        return response()->result(new CouponPaginateCollection($items));
    }

    public function create(Request $request)
    {
        $data = $request->validated();
        $data['expired_at'] = toCarbon(array_get($data, 'expired_at'));
        $data['status'] = Coupon::STATUS_ACTIVE;
        $item = $this->couponRepository->create($data);
        $item->users_count = 0;

        return response()->result(new CouponPaginateResourse($item));
    }

    public function get(Coupon $coupon, Request $request)
    {
        $coupon->load(['users' => function($q){
            $q->orderBy('coupon_user.created_at', 'desc');
        }]);

        return response()->result(new CouponWithUsersResourse($coupon));
    }

    public function pauseCoupon(Coupon $coupon, Request $request)
    {
        customThrowIf( ! $coupon->isActive(), 'Coupon is '.$coupon->status);
        $item = $this->couponRepository->update($coupon, [
            'status' => Coupon::STATUS_PAUSED,
        ]);
        $item->loadCount(['users'])->load(['users']);
        $item->users->each(function($user){
            $user->update([
                'is_paid' => false,
                'coupon_to' => null,
            ]);
            event(new UserBecomeFreeEvent($user));
        });

        return response()->result(new CouponPaginateResourse($item), 'Coupon paused');
    }

    public function unpauseCoupon(Coupon $coupon, Request $request)
    {
        customThrowIf( ! $coupon->isPaused(), 'Coupon is '.$coupon->status);
        $item = $this->couponRepository->update($coupon, [
            'status' => Coupon::STATUS_ACTIVE,
        ]);
        $item->loadCount(['users'])->load(['users']);
        $item->users->each(function($user){
            $expiderAt = toCarbon($user->pivot->expired_at);
            if($expiderAt->gt(now())) {
                $user->update([
                    'is_paid' => true,
                    'coupon_to' => $expiderAt,
                ]);
                event(new UserBecomePaidEvent($user));
            }
        });
        return response()->result(new CouponPaginateResourse($item));
    }

    public function deleteCoupon(Coupon $coupon, Request $request)
    {
        customThrowIf( $coupon->isDeleted(), 'Coupon is '.$coupon->status);
        $item = $this->couponRepository->update($coupon, [
            'status' => Coupon::STATUS_DELETED,
        ]);
        $item->loadCount(['users'])->load(['users']);
        $item->users->each(function($user){
            $user->update([
                'is_paid' => false,
                'coupon_to' => null,
            ]);
            event(new UserBecomeFreeEvent($user));
        });

        return response()->result(new CouponPaginateResourse($item));
    }
}
