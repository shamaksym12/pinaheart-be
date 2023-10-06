<?php

namespace App\Services\Client;

use Illuminate\Http\Request;
use Socialite;
use App\Services\CoreService;
use GuzzleHttp\Client;
use App\Repositories\UserRepository;
use App\Repositories\CouponRepository;
use App\User;
use App\Hash;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\Client\User\Login as UserLoginResourse;
use App\Events\Auth\Register as AuthRegisterEvent;
use App\Events\Auth\Recovery as AuthRecoveryEvent;
use App\Services\StorageService;
use App\Mail\User\ReqeustUnBlockUser;
use Illuminate\Support\Facades\Mail;

class AuthService extends CoreService
{
    protected $userRepository;
    protected $couponRepository;
    protected $storageService;

    public function __construct(
        UserRepository $userRepository,
        CouponRepository $couponRepository,
        StorageService $storageService)
    {
        $this->userRepository = $userRepository;
        $this->couponRepository = $couponRepository;
        $this->storageService = $storageService;
    }

    public function login(Request $request)
    {
        $data = $request->only(['email', 'password']);
        if (auth()->attempt($data)) {
            $me = auth()->user()->loadCount(['photos']);

            //customThrowIf( ! $me->isActive(), 'Account isn\'t active', 422);

            if ($me->is_admin_block) {
                customThrow(__('An administrator has blocked your account'), 423);
            }

            $tokens = $this->getPassportTokens($data);

            $result = (new UserLoginResourse($me))->withCustomData(compact('tokens'));

            return response()->result($result, __('You was successfully logged in'));
        } else {
            customThrow(__('Incorrect email or password'), 422);
        }
    }

    public function loginByHash($request) 
    {
        $hash = $request->hash;
        if ($hash) {
            $user = User::where(DB::raw('md5(id)'), $hash)->first();              
            $currentPassword = $user->password;
            $tmpPassword = 1234;

            $user->password = bcrypt($tmpPassword);
            $user->save();
            
            auth()->login($user);
            $me = auth()->user()->loadCount(['photos']);
            
            $data = [
                'email' => $user->email,
                'password' => $tmpPassword,
            ];

            $tokens = $this->getPassportTokens($data);
            $result = (new UserLoginResourse($me))->withCustomData(compact('tokens'));

            $user->password = $currentPassword;
            $user->save();
            return response()->result($result, __('You was successfully logged in'));
        } else {
            customThrow(__('Incorrect hash'), 422);
        }
    }

    public function unblock(Request $request) 
    {
        $data = $request->only(['device', 'comment', 'attach', 'location', 'email']);        
        $data['user'] = User::where('email', $data['email'])->first();
        if ( ! empty($data['attach'])) {
            $data['attach'] = url("storage/" . $data['attach']->store('public'));
        }
        
        if ( empty($data['user'])) {
            customThrow(__('User not found!'), 400);
        }
        
        Mail::to(env('EMAIL_REQUEST_ACCOUNT_UNBLOCK'))->send(new ReqeustUnBlockUser($data));
        return response()->result(true, __('Successfully submitted. A staff member will contact you shortly.'));
    }

    public function register(Request $request)
    {
        $dataUser = $request->validated();
        $password = $dataUser['password'];
        $dataUser['password'] = bcrypt($password);
        $dataUser['first_name'] = ucfirst($request->first_name);
        $dataUser['status'] = User::STATUS_ACTIVE;
        $dataUser['role'] = User::ROLE_USER;

        $user = $this->userRepository->create($dataUser);
        if(($couponCode = $request->coupon) &&  ($coupon = $this->couponRepository->firstByCode($couponCode))) {
            $expiredAt = now()->addDays($coupon->count_days);
            $user->coupons()->attach([
                $coupon->id => [
                    'started_at' => now(),
                    'expired_at' => $expiredAt,
                ],
            ]);
            $user = $this->userRepository->update($user, [
                'is_paid' => true,
                'coupon_to' => $expiredAt,
            ]);
        }
        $tokens = $this->getPassportTokens(['email' => $dataUser['email'], 'password' => $password]);

        event(new AuthRegisterEvent($user));
        $user->loadCount(['photos']);
        $result = (new UserLoginResourse($user))->withCustomData(compact('tokens'));

        return response()->result($result, 'Your account was successfully created');
    }

    public function recovery(Request $request)
    {
        $user = $this->userRepository->firstByEmail($request->email);

        customThrowIf( ! $user, 'There\'s no user with such email. Please, sign up', 422);

        customThrowIf( ! $user->isActive(), 'Account is not activated', 422);

        $hash = $this->userRepository->addRandomHash($user, Hash::TYPE_RECOVERY);
        $recoveryLink = str_replace('{hash}', $hash, $request->link);

        event(new AuthRecoveryEvent($user, $recoveryLink));

        return response()->result(true, 'Link for password recovery is sent. Please, check your emai');
    }

    public function reset(Request $request)
    {
        $user = $this->userRepository->getItemByHash($request->hash, Hash::TYPE_RECOVERY);
        customThrowIf( ! $user, 'Wrong link');
        customThrowIf( ! $user->isActive(), 'Account is not activated', 422);

        $data = [
            'password' => bcrypt($request->password),
        ];
        $user = $this->userRepository->update($user, $data);
        $passportData = array_merge(['email' => $user->email, 'password' => $request->password]);
        $tokens = $this->getPassportTokens($passportData);
        $user->loadCount(['photos']);
        $result = (new UserLoginResourse($user))->withCustomData(compact('tokens'));
        return response()->result($result, 'You password was changed');
    }

    public function redirect(Request $request)
    {
        switch ($request->provider) {
            case User::SOCIALITE_FACEBOOK:
                $url = Socialite::driver(User::SOCIALITE_FACEBOOK)->fields([
                    'first_name', 'last_name', 'email', 'gender', 'birthday'
                ])->scopes(['user_photos','user_location','user_gender','user_birthday'])->stateless()->redirect()->getTargetUrl();
                break;
            case User::SOCIALITE_GOOGLE:
                $url = Socialite::driver(User::SOCIALITE_GOOGLE)->stateless()->redirect()->getTargetUrl();
                break;

        }
        return response()->result($url, 'Redirect url');
    }

    public function google(Request $request)
    {
        try {
            $socUser = Socialite::driver('google')->stateless()->user();
            customThrowIf(empty($socUser->email), 'You must set email in your account to login');
            $localUser = $this->userRepository->firstByEmail($socUser->email);

            if ($localUser) {
                return $this->loginExistsUser($localUser);
            } else {
                $tempPassword = str_random(10);
                $socUserData = $socUser->user;
                $dataUser = [
                    'email' => $socUser->email,
                    'password' => bcrypt($tempPassword),
                    'status' => User::STATUS_ACTIVE,
                    'first_name' => array_get($socUserData, 'given_name'),
                    'last_name' =>  array_get($socUserData, 'family_name'),
                    'email_verified_at' => now(),
                    'is_soc_user' => true,
                    'role' => User::ROLE_USER,
                ];
                $gender = array_get($socUserData, 'gender');
                $dataUser['sex'] = $gender == 'male' ? 'M' : 'F';

                $localUser = $this->userRepository->create($dataUser);

                $picture = array_get($socUserData, 'picture');
                if($picture) {
                    $dataPhoto = $this->storageService->loadAnotherAvatar($picture);
                    $dataPhoto['is_main'] = true;
                    $this->userRepository->createUserPhoto($localUser, $dataPhoto);
                }

                $tokens = $this->getPassportTokens(['email' => $localUser->email, 'password' => $tempPassword]);

                $this->userRepository->update($localUser, ['password' => '']);
                $localUser->loadCount(['photos']);
                $result = (new UserLoginResourse($localUser))->withCustomData(compact('tokens'));
                return response()->result($result, 'You was successfully logged in');
            }
        } catch(\Exception $e) {
            customThrow($e->getMessage());
        }
    }

    public function facebook(Request $request)
    {
        try {
            $socUser = Socialite::driver('facebook')->fields([
                'first_name', 'last_name', 'email', 'gender', 'birthday'
            ])->stateless()->user();
            $socUserData = $socUser->user;
            if(empty($socUser->email)) {
                $dob = toCarbon(array_get($socUserData, 'birthday'));
                $result = [
                    'no_email' => true,
                    'first_name' => array_get($socUserData, 'first_name'),
                    'last_name' => array_get($socUserData, 'last_name'),
                    'age' => $dob ? now()->diffInYears($dob) : null,
                    'sex' => array_get($socUserData, 'gender') == 'male' ? 'M' : 'F',
                ];
                return response()->result($result, 'Thank You! You still need to complete the registration!');
            }
            $localUser = $this->userRepository->firstByEmail($socUser->email);

            if ($localUser) {
                return $this->loginExistsUser($localUser);
            } else {
                $tempPassword = str_random(10);
                $dataUser = [
                    'email' => $socUser->email,
                    'password' => bcrypt($tempPassword),
                    'status' => User::STATUS_ACTIVE,
                    'first_name' => array_get($socUserData, 'first_name'),
                    'last_name' => array_get($socUserData, 'last_name'),
                    'dob' => toCarbon(array_get($socUserData, 'birthday')),
                    'email_verified_at' => now(),
                    'is_soc_user' => true,
                    'role' => User::ROLE_USER,
                ];
                $gender = array_get($socUserData, 'gender');
                if($gender) {
                    $dataUser['sex'] = $gender == 'male' ? 'M' : 'F';
                }

                $localUser = $this->userRepository->create($dataUser);

                $picture = $socUser->avatar;
                if($picture) {
                    $dataPhoto = $this->storageService->loadAnotherAvatar($picture);
                    $dataPhoto['is_main'] = true;
                    $this->userRepository->createUserPhoto($localUser, $dataPhoto);
                }

                $tokens = $this->getPassportTokens(['email' => $localUser->email, 'password' => $tempPassword]);

                $this->userRepository->update($localUser, ['password' => '']);
                $localUser->loadCount(['photos']);
                $result = (new UserLoginResourse($localUser))->withCustomData(compact('tokens'));
                return response()->result($result, 'You was successfully logged in');
            }
        } catch(\Exception $e) {
            customThrow($e->getMessage());
        }
    }

    protected function loginExistsUser(User $user)
    {
        $oldPassword = $user->password;
        $tempPassword = str_random(10);
        $this->userRepository->update($user, ['password' => bcrypt($tempPassword)]);
        $tokens = $this->getPassportTokens(['email' => $user->email, 'password' => $tempPassword]);
        $this->userRepository->update($user, ['password' => $oldPassword]);
        $user->loadCount(['photos']);
        $result = (new UserLoginResourse($user))->withCustomData(compact('tokens'));
        return response()->result($result, 'You was successfully logged in');
    }

    protected function getPassportTokens(array $data)
    {
        $client = new Client(['verify' => false]);

        $passwordParams = [
            'form_params' => [
                'grant_type' => 'password',
                'client_id' => config('auth.passport.grand.client_id'),
                'client_secret' => config('auth.passport.grand.client_secret'),
                'username' => array_get($data, 'email'),
                'password' => array_get($data, 'password'),
                'scope' => '*',
            ],
            'http_errors' => false,
        ];

        $response = $client->post(url('oauth/token'), $passwordParams);

        $result = json_decode($response->getBody()->getContents());

        customThrowIf( ! $result || empty($result->access_token), $result->message ?? __('Can\'t get token'));

        return $result;
    }
}
