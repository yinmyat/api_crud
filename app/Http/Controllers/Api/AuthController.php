<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Models\User;
use DB;

class AuthController extends Controller
{
    public function register() {
       
        $credentials = request(['name','email','password','password_confirmation']);
        
        $validator = $this->validator($credentials)->validate();
        
        DB::beginTransaction();
        try{

            $user = User::create([
                'name' => $credentials['name'],
                'email' => $credentials['email'] ?? null,
                'password' => Hash::make($credentials['password']),
            ]);

            if(isset($credentials['email'])) {
                $token = \JWTAuth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']]);
            }
           
            DB::commit();
            
        }catch(\Exception $e){
            DB::rollback();
            return response()->json([
                'status' => false,
                'message' => 'Your request is invalid!'
            ],400);
        }

        return $this->respondWithToken($token, $user->name,"Registration Successful.","Thank you for creating your account.");

    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'email' => 'required',
            'password' => 'required'
        ]);
 
        if ($validator->fails()) {
            return response()->json(['status' => false,'message'=>'Account is not found!Please Sign up!'],422);
        }
        
        $credentials = $this->credentials($request);
        
        $token = null;
        if (!$token = \JWTAuth::attempt($credentials)) {

                return response()->json([
                    'status' => false,
                    'message'  => "Login fails"
                ], 401);
        }
        $token = \JWTAuth::attempt($credentials);
        \Log::debug('token'.$token);
            $user = \Auth::user();
            $user->remember_token = $token;
        $user->save();

        \Log::debug('loggedin here');

        return $this->respondWithToken($token,$user->name,"Authenticated!","You have successfully login.");
    }

    public function logout()
    {
        \Cache::forget('active-' . \Auth::user()->id);
        auth()->logout();
        \JWTAuth::invalidate(\JWTAuth::getToken());

        return response()->json(['status'  => true,'message' => 'Successfully logged out'], 200);
    }

    protected function credentials(Request $request)
    {
        if (filter_var($request->input('email'), FILTER_VALIDATE_EMAIL)) {
            $field = 'email';
        }else{
            $field = 'name';
        }
        return [
            $field => $request->input('email'),
            'password' => $request->get('password'),
        ];
    }

    protected function respondWithToken($token,$user_name,$title, $message)
    {
        return response()->json([
            'status' => true,
            'title' => $title,
            'message' => $message,
            'data' => [
                    'access_token' => $token,
                    'token_type' => 'bearer',
                    'user_name' => $user_name,
                    'expires_in' => auth('api')->factory()->getTTL() * 60
                ]
            ],200);
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [ 
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'nullable','string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }
}
