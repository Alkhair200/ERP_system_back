<?php

namespace App\Http\Controllers\Api;

use Crypt;
use JWTAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Traits\GeneralTrait;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use App\Http\Controllers\Controller;


class AuthController extends Controller
{
    use GeneralTrait;
    // public function __construct()
    // {
    //     $this->middleware('auth:api', ['except' => ['login']]);
    // }

    public function login(Request $request)
    {

        $validate = Validator::make($request->all(),[
            'email' => 'required|email',
            'password' => 'required|min:4',
        ]);

        if ($validate->fails()) {
            return $this->returnValidationError(404,$validate);
        } 
        if (!$token = JWTAuth::attempt($validate->validated())) {
            return $this->returnError(401,'بيانات الدخول غير صحيحة');
        }

        return $this->createNewToken($token);
    }

    
    public function logout(Request $request)
    {

        $token = $request['token'];

        if ($token) {

            try {

                auth()->logout();
                // borken access controller or user enumeratoion
                JWTAuth::setToken($token)->invalidate(); // logout
                
            } catch (TokenInvalidException $e) {
                
                return $this->returnError('E0001' , 'some thing want wrong');

            }

            return $this->returnSuccessMessage('تم ستجيل الخروج بنجاح');

        } else {
            
            return $this->returnError('E0001' , 'some thing want wrong');
        }        
    }


    public function me()
    {
        return response()->json(auth()->user());
    }

    
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }


    protected function createNewToken($token)
    {
        return $this->returnUserAuth($token , auth()->user());
    }  
}
