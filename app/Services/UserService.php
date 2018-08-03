<?php

namespace App\Services;

use App\Repositories\Contracts\UserRepositoryInterface;
use App\Http\Controllers\API\ApiBaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\JWTAuth;
use Log;

/**
 * Class UserService
 * @package App\Services
 */
class UserService extends  ApiBaseController
{
    protected $auth;

    public function __construct(JWTAuth $auth)
    {
        $this->auth = $auth;
    }
    
    public function returnUserLoginStatus($user)
    {
        $data = $this->verifyUserToken($user);
        $msg = ['login.log_in'];
        
        return $this->tokenResponse($data, $msg);
    }

    public function verifyUserToken($user)
    {
        $data = null;
        $token = null;
        try {
            if (! $token = $this->auth->fromUser($user)) {
                $data = [
                    'code' => 422,
                    'message' => 'Invalid OTP',
                ];
            }
        } catch (JWTAuthException $e) {
            $data = [
                'code' => 500,
                'message' => $e->getMessage(),
            ];
        }

        $data = [
            'message' => 'Number Verified Successfully!!',
            'token' => $token,
            'user_mobile_number' => $user->phone_no
        ];
        return $data;
    }

    public function tokenResponse($data, $msg)
    {
        if (array_key_exists('token', $data)) {
            return $this
                ->setStatusCode(200)
                ->setDataBag($data)
                ->respond(true, trans($msg[0]));
        } else {
            return $this
                ->setStatusCode(422)
                ->setDataBag($data)
                ->respond(false, trans('login.unable_token_generation'));
        }
    }


}