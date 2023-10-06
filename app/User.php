<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Http\Resources\Client\User\Photo as UserPhotoResourse;
use Laravel\Passport\HasApiTokens;
use App\Repositories\ProfileRepository;
use Illuminate\Support\Facades\DB;
use App\Events\User\BecomePaid;
use App\Events\User\BecomeFree;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

    const ADMIN_PAGINATE_PER_PAGE = 20;
    const FRONT_PAGINATE_PER_PAGE = 24;

    const STATUS_NEW = 'new';
    const STATUS_ACTIVE = 'active';
    const STATUS_BLOCKED = 'blocked';

    const ROLE_ADMIN = 'admin';
    const ROLE_MANAGER = 'manager';
    const ROLE_JUNIOR = 'junior';
    const ROLE_USER = 'user';

    const SOCIALITE_FACEBOOK = 'facebook';
    const SOCIALITE_GOOGLE = 'google';
    const SOCIALITE_PROVIDERS = [self::SOCIALITE_FACEBOOK, self::SOCIALITE_GOOGLE];

    protected $fillable = [
        'status',
        'role',
        'is_paid',
        'subscribe',
        'old_subscribe_to',
        'coupon_to',
        'profile_id',
        'first_name',
        'last_name',
        'email',
        'password',
        'sex',
        'age',
        'dob',
        'is_soc_user',
        'last_activity_at',
        'is_off',
        'is_busy',
        'is_hidden',
        'comment',
        'is_admin_block',
    ];

    protected $dates = [
        'dob',
        'last_activity_at',
        'old_subscribe_to',
        'coupon_to',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_paid' => 'boolean',
        'is_soc_user' => 'boolean',
        'email_verified_at' => 'datetime',
        'is_off' => 'boolean',
        'is_busy' => 'boolean',
        'is_hidden' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model)
        {
            $profile_id = randomInteger(8);
            while(self::where('profile_id', $profile_id)->exists()) {
                $profile_id = randomInteger(8);
            }
            $model->profile_id = $profile_id;
        });
        static::saving(function ($model)
        {
            if($model->isDirty('dob')) {
                $oldAge = $model->age;
                $model->age = optional($model->dob)->diffInYears(now());
            }
            if($model->isDirty('sex') && ($sex = $model->sex)) {
                $model->match()->update([
                    'sex' => $sex == 'F' ? 'M' : 'F',
                ]);
            }
        });
    }

    /**Start Relations */
    public function hashes()
    {
        return $this->morphToMany('App\Hash', 'hashable')->withTimestamps();
    }

    public function photos()
    {
        return $this->hasMany(Photo::class);
    }

    public function mainPhoto()
    {
        return $this->hasOne(Photo::class)->where('is_main', true)->approved(true);
    }

    public function profileParamsValues()
    {
        return $this->hasMany(UserParam::class);
    }

    public function profileParams()
    {
        return $this->belongsToMany(Param::class, 'user_params')->withPivot('value', 'value_id')->withTimestamps();
    }

    public function location()
    {
        return $this->hasOne(UserLocation::class);
    }

    public function info()
    {
        return $this->hasOne(UserInfo::class);
    }

    public function off()
    {
        return $this->hasOne(UserOff::class);
    }

    public function interest()
    {
        return $this->hasOne(UserInterest::class);
    }

    public function match()
    {
        return $this->hasOne(UserMatch::class);
    }

    public function matchParams()
    {
        return $this->belongsToMany(Param::class, 'user_match_params')->withPivot('value', 'value_id')->withTimestamps();
    }

    public function personality()
    {
        return $this->hasOne(UserPersonality::class);
    }

    public function notifySettings()
    {
        return $this->hasMany(UserNotifySetting::class);
    }

    public function searches()
    {
        return $this->hasMany(UserSearch::class);
    }

    public function sentActivityUsers()
    {
        return $this->belongsToMany(User::class, 'activities', 'who_id', 'whom_id')->withTimestamps()->withPivot('type');
    }

    public function inboxActivityUsers()
    {
        return $this->belongsToMany(User::class, 'activities', 'whom_id', 'who_id')->withTimestamps()->withPivot('type');
    }

    public function favoritesUsers()
    {
        return $this->belongsToMany(User::class, 'user_favorites', 'who_id', 'whom_id')->withTimestamps();
    }

    public function favoritedByUsers()
    {
        return $this->belongsToMany(User::class, 'user_favorites', 'whom_id', 'who_id')->withTimestamps();
    }

    public function interestedUsers()
    {
        return $this->belongsToMany(User::class, 'user_interest_users', 'who_id', 'whom_id')->withTimestamps();
    }

    public function interestedByUsers()
    {
        return $this->belongsToMany(User::class, 'user_interest_users', 'whom_id', 'who_id')->withTimestamps();
    }

    public function blockedUsers()
    {
        return $this->belongsToMany(User::class, 'user_blocked_users', 'who_id', 'whom_id')->withTimestamps();
    }

    public function blockedByUsers()
    {
        return $this->belongsToMany(User::class, 'user_blocked_users', 'whom_id', 'who_id')->withTimestamps();
    }

    public function adminData()
    {
        return $this->hasOne(UserAdminData::class);
    }

    public function sentMessages()
    {
        return $this->belongsToMany(Message::class, 'view_user_sent_message');
    }

    public function inboxMessages()
    {
        return $this->belongsToMany(Message::class, 'view_user_inbox_message');
    }

    public function inboxUnreadMessages()
    {
        return $this->belongsToMany(Message::class, 'view_user_unread_inbox_message');
    }

    public function stripeSubscriber()
    {
        return $this->hasOne(StripeSubscriber::class);
    }

    public function stripeSubscriptions()
    {
        return $this->hasMany(StripeSubscription::class);
    }

    public function stripePayments()
    {
        return $this->hasMany(StripePayment::class);
    }

    public function paypalSubscriptions()
    {
        return $this->hasMany(PaypalSubscription::class);
    }

    public function paypalPayments()
    {
        return $this->hasMany(PaypalPayment::class);
    }

    public function coupons()
    {
        return $this->belongsToMany(Coupon::class)->withTimestamps()->withPivot(['started_at', 'expired_at']);
    }

    public function showedAutoRenewal()
    {
        return $this->hasMany(ShowedAutoRenewal::class, 'user_id');
    }

    public function showedExpiredModal()
    {
        return $this->hasMany(ShowedExpiredModal::class, 'user_id');
    }
    /**End Relations */

    /**Start Scopes*/
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeWithOutBlocked($query)
    {
        return $query->where('is_admin_block', false);
    }

    public function scopeRole($query, $role)
    {
        return $query->where('role', $role);
    }

    public function scopeOff($query, bool $value)
    {
        return $query->where('is_off', $value);
    }

    public function scopeHidden($query, bool $value)
    {
        return $query->where('is_hidden', $value);
    }

    public function scopeWithLastActivityDiff($query)
    {
        
        return $query->selectRaw("*, (TIMESTAMPDIFF(second, last_activity_at, now())) as activity_diff_in_seconds");
    }


    public function scopeForList($query, User $user)
    {

        $blockedUserIds = DB::table('user_blocked_users')->select('who_id')->where('whom_id', $user->id)->get()->pluck('who_id')->toArray();
        $notIds = array_merge($blockedUserIds, [$user->id]);
        return $query->active()->off(false)->hidden(false)->withLastActivityDiff()->role(self::ROLE_USER)->whereNotIn('id', $notIds)->with([
            'mainPhoto',
            'location.country',
            'match',
            'blockedByUsers' => function($q) use($user){
                $q->where('who_id', $user->id);
            },
            'coupons' => function($q){
                $q->where('coupon_user.expired_at', '>', now());
            },
        ])->withCount(['photos' => function($q){
            $q->approved();
        }]);
    }

    public function scopeForDialog($query, User $user)
    {
        return $query->with([
            'location.country',
            'mainPhoto',
        
            'match',
            'favoritedByUsers' => function($q) use($user){
                $q->where('who_id', $user->id);
            },
            'interestedByUsers' => function($q) use($user){
                $q->where('who_id', $user->id);
            },
            'blockedByUsers' => function($q) use($user){
                $q->where('who_id', $user->id);
            },
        ]);
    }

    public function scopeSearch($query, array $data)
    {
        ($first_name = array_get($data, 'first_name')) ? $query->where('first_name', 'like', '%'.$first_name.'%') : null;
        ($profile_id = array_get($data, 'profile_id')) ? $query->where('profile_id', $profile_id) : null;
        ($age_from = array_get($data, 'age_from')) ? $query->where('age', '>=', $age_from) : null;
        ($age_to = array_get($data, 'age_to')) ? $query->where('age', '<=', $age_to) : null;
        ($sex = array_get($data, 'sex')) ? $query->where('sex', $sex) : null;

        if($dataParams = array_get($data, 'params')) {
            $profileRepository = new ProfileRepository();
            $aliases = array_keys($dataParams);
            $params = $profileRepository->getParamsByAliases($aliases);
            $handParams = ['education', 'english_language_ability', 'relationship_youre_looking_for'];
            $notFixed = $params->where('type_search', '<>', Param::TYPE_FIXED)->whereNotIn('alias', $handParams);
            //education
            if($e = array_get($dataParams, 'education')){
                $eValue = array_first($e);
                $query->whereHas('profileParams', function($q) use($eValue){
                    $q->where('alias', 'education');
                    $q->where('value_id', '>=', $eValue);
                });
            }
            //english_language_ability
            if($el = array_get($dataParams, 'english_language_ability')){
                $elValue = array_first($el);
                $query->whereHas('profileParams', function($q) use($elValue){
                    $q->where('alias', 'english_language_ability');
                    $q->where('value_id', '>=', $elValue);
                });
            }
            //relationship_youre_looking_for
            if($rylf = array_get($dataParams, 'relationship_youre_looking_for')){
                $paramRylf = $params->firstWhere('alias', 'relationship_youre_looking_for');
                $any = $profileRepository->findParamValueByName($paramRylf, 'Any');
                $ids = array_merge($rylf, [$any->id]);
                $query->whereHas('profileParamsValues', function($q) use($paramRylf, $ids){
                    $q->where('param_id', $paramRylf->id);
                    $q->whereIn('value_id', $ids);
                });
            };
            //all not fixed
            foreach($notFixed as $item) {
                $values = array_get($dataParams, $item->alias);
                $query->whereHas('profileParamsValues', function($q) use($item, $values){
                    $q->where('param_id', $item->id);
                    $q->whereIn('value_id', $values);
                });
            }
            //height
            $height_from = array_get($dataParams, 'height_from');
            $height_to = array_get($dataParams, 'height_to');
            if($height_from) {
                $query->whereHas('profileParams', function($q) use($height_from){
                    $q->where('alias', 'height');
                    $height_from ? $q->where('value', '>=', (int)$height_from) : null;
                });
            }

            if($height_to) {
                $query->whereHas('profileParams', function($q) use($height_to){
                    $q->where('alias', 'height');
                    $height_to ? $q->where('value', '<=', (int)$height_to) : null;
                });
            }
            //weight
            $weight_from = array_get($dataParams, 'weight_from');
            $weight_to = array_get($dataParams, 'weight_to');
            if($weight_from || $weight_to) {
                $query->whereHas('profileParams', function($q) use($weight_from, $weight_to){
                    $q->where('alias', 'weight');
                    $weight_from ? $q->where('value', '>=', (int)$weight_from) : null;
                    $weight_to ? $q->where('value', '<=', (int)$weight_to) : null;
                });
            }
        }
        if ($country_id = array_get($data, 'country_id')) {
            $query->whereHas('location', function($q) use($country_id){
                $q->where('country_id', $country_id);
            });
        }
        
        if(($lat = array_get($data, 'lat')) && ($long = array_get($data, 'long'))) {
            $distance = array_get($data, 'distance') ?? 0;
            $distance_unit = array_get($data, 'distance_unit') ?? 'km';
            if($distance_unit == 'km' && $distance) {
                $sqlDistance = 60*1.1515*1.609344;
            } else {
                $sqlDistance = 60*1.1515;
            }
            $query->whereHas('location', function($q) use($lat, $long, $distance, $sqlDistance){
                $q->whereRaw('(((acos(sin(('.$lat.'*pi()/180)) *
                sin((lat*pi()/180))+cos(('.$lat.'*pi()/180)) *
                cos((lat*pi()/180)) * cos((('.$long.'-
                `long`)*pi()/180))))*180/pi())*'.$sqlDistance.') <= '.$distance);
            });
        }                
        (array_get($data, 'with_photo')) ? $query->whereHas('photos', function($q){
            $q->approved();
        }) : null;
        //by match
        if($iam = array_get($data, 'iam')) {
            $query->whereHas('match', function($q) use($iam){
                $q->where('sex', $iam);
                $q->orWhereNull('sex', null);
            });
        }        
        if($max_match_age_to = array_get($data, 'max_match_age_to')) {
            $query->whereDoesntHave('match', function($q) use($max_match_age_to){
                $q->where(function($q2) use($max_match_age_to){
                    $q2->where('age_to', '<', $max_match_age_to);
                    // $q2->orWhereNull('age_to');
                });
            });
        }
        //last_active
        if($last_active = array_get($data, 'last_active')) {
            $endActivityDate = null;
            switch ($last_active) {
                case 'week':
                    $endActivityDate = now()->subWeek();
                    break;
                case '2week':
                    $endActivityDate = now()->subWeeks(2);
                    break;
                case 'month':
                    $endActivityDate = now()->subMonth();
                    break;
                case '3month':
                    $endActivityDate = now()->subMonths(3);
                    break;
                case '6month':
                    $endActivityDate = now()->subMonths(6);
                    break;
                case 'year':
                    $endActivityDate = now()->subYear();
                    break;
            }
            $endActivityDate ? $query->where('last_activity_at', '>', $endActivityDate) : null;
        }       
        //online
        if(array_get($data, 'only_online')) {
            $query->online();
        }
        //admin_blocked
        if(array_get($data, 'admin_blocked')) {
            $query->where('is_admin_block', $data['admin_blocked']);
        }
        //with staff comments
        if(array_get($data, 'with_admin_comments')) {
            if ($data['with_admin_comments']) {
                $query->where('comment', '!=', '');
            }
        }
        
        //sort
        $query->sort($data);
    }

    public function scopeSort($query, array $data)
    {
        if($sort = array_get($data, 'sort')) {
            switch ($sort) {
                case 'last_active':
                    $query->orderBy('last_activity_at', 'desc');
                    break;
                case 'newest':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'youngest':
                    $query->orderBy('age', 'asc');
                    break;
            }
        }
    }

    public function scopeOnline($query)
    {
        return  $query->where('last_activity_at', '>=', now()->subMinutes(3));
    }
    /**End Scopes */

    /**Start Mutators*/
    public function getAccountStatusAttribute()
    {
        if( ! $coupon = $this->coupons->first()) {
            return $this->is_paid ? 'platinum' : 'free';
        } else {
            if($this->is_paid) {
                return 'free_platinum';
            }
            if($coupon->isPaused()) {
                return 'free_platinum_on_hold';
            }
            return 'free';
        }
    }

    public function getAccountStatusTextAttribute()
    {
        switch ($this->account_status) {
            case 'platinum':
                return 'Platinum';
                break;
            case 'free':
                return 'Free';
                break;
            case 'free_platinum':
                return 'Free platinum';
                break;
            case 'free_platinum_on_hold':
                return 'Free platinum on hold';
                break;
            default:
                return '';
                break;
        }
    }

    public function getFreePlatinumUntilAttribute()
    {
        if( ! $coupon = $this->coupons->first()) {
            return null;
        } else {
            return $coupon->isActive() ? $coupon->pivot->expired_at : null;
        }
    }
    /**End Mutators */

    /**Start Helper*/
    public function isActive()
    {
        return $this->status == self::STATUS_ACTIVE;
    }

    public function isSocialUser()
    {
        return (bool)$this->is_soc_user;
    }

    public function isAdmin()
    {
        return $this->role == self::ROLE_ADMIN;
    }

    public function isManager()
    {
        return $this->role == self::ROLE_MANAGER;
    }

    public function isJunior()
    {
        return $this->role == self::ROLE_JUNIOR;
    }

    public function isMale()
    {
        return $this->sex == 'M';
    }

    public function isFemale()
    {
        return $this->sex == 'F';
    }

    public function isOff()
    {
        return (bool) $this->is_off;
    }

    public function isPaid()
    {
        return (bool) $this->is_paid;
    }

    public function isSubscriber()
    {
        return is_null($this->subscribe);
    }

    public function getDefaultMatch()
    {
        return [
            'sex' => ($this->sex == 'F') ? 'M' : 'F',
            'age_from' => null,
            'age_to' => null,
            'country_id' => null,
            'formatted_address' => null,
            'place_id' => null,
            'lat' => null,
            'long' => null,
            'distance' => null,
            'distance_unit' => null,
        ];
    }

    public function hasDefaultMatch()
    {
        $userMatch = array_except($this->match->toArray(), ['id', 'user_id','created_at', 'updated_at']);
        return ($userMatch == $this->getDefaultMatch()) ? ! $this->matchParams()->exists() : false;
    }

    public function setActivity()
    {
        $this->forceFill(['last_activity_at' => $this->freshTimestamp()])->save();
    }
    /**End Helper*/
}
