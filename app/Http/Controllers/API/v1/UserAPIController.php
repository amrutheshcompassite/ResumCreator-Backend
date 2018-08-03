<?php

namespace App\Http\Controllers\API\v1;

use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Http\Request;
use App\Http\Controllers\API\ApiBaseController;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Services\UserService;
use Tymon\JWTAuth\JWTAuth;
class UserAPIController extends ApiBaseController
{

    protected $userRepository;

    protected $userService;

    protected $auth;

    public function __construct(UserRepositoryInterface $userRepository,
                                UserService $userService,
                                JWTAuth $auth)
    {
        $this->userRepository = $userRepository;
        $this->userService = $userService;
        $this->auth = $auth;
    }

    /**
     * Get User's list
     */
    public function index()
    {
        $users = User::all();

        dd($users);
    }

    public function userRegister()
    {
        $appinput = $this->getContent();

        $rules = [
            'phone_no' => 'required|regex:[^[0-9]*$]'
        ];

        $validator = Validator::make($appinput, $rules);

        if ($validator->fails()) {
            return $this
                ->setStatusCode(422)
                ->setDataBag($validator->errors()->toArray())
                ->respond(false, trans('login.number_exists'));
        } else {
            $userCheck = $this->userRepository->getUser($appinput['phone_no']);
            if(!$userCheck)
            {
                $newUser = $this->userRepository->storeUser($appinput);
                return $this
                    ->setStatusCode(200)
                    ->setDataBag($newUser)
                    ->respond(true, trans('login.new_user_created'));
            }else{
                return $this
                    ->setStatusCode(422)
                    ->respond(false, trans('user.already_exsists'));
            }
        }
        
    }

    public function login()
    {
        $appInput = $this->getContent();
        $rules = [
            'phone_no' => 'required|regex:[^[0-9]*$]',
//            'password' => 'required'
        ];
        $validator = Validator::make($appInput, $rules);
        if ($validator->fails()) {
            return $this
                ->setStatusCode(422)
                ->setDataBag($validator->errors()->toArray())
                ->respond(false, trans('login.validation_error'));
        } else {
//            if (!$token = $this->auth->attempt($appInput)) {
//                return $this
//                    ->setStatusCode(422)
//                    ->respond(false, 'Invalid Credentials.');
//            }
            $user = $this->userRepository->getUser($appInput['phone_no']);
            return $this->userService->returnUserLoginStatus($user);
        }
    }

    public function checkJWT()
    {
        dd("done");
    }
}
