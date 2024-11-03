<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\BaseController;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends BaseController

{

    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required',
            'username' => 'required',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] =  $user->createToken('MyApp')->plainTextToken;
        $success['employee'] = $user->employee;

        return $this->sendResponse($success, 'User register successfully.');
    }

    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $user = User::with(['employee','role','branch'])->where('username', $request->username)->first();

        $credentials = request(['username', 'password']);
        if (!Auth::attempt($credentials)) {
            return response([
                'success'   => false,
                'message' => ['These credentials do not match our records.']
            ], 404);
        }

        $token = $user->createToken('Api-token')->plainTextToken;

        $response = [
            'success'   => true,
            'user'      => $user,
            'token'     => $token,
            'message'   => 'Berhasil Login'
        ];
        return response($response, 201);
    }


    // public function logout(Request $request)
    // {
    //     // Get bearer token from the request
    //     $accessToken = $request->bearerToken();
    //     // Get access token from database
    //     $token = PersonalAccessToken::findToken($accessToken);
    //     if($token){
    //         // Revoke token
    //         $token->delete();
    //         return $this->sendResponse('done', 'User logout successfully.');
    //     }
    //     return $this->sendResponse('error','token not found');

    // }

    public function logout(Request $request)
    {
        Auth::user()->tokens()->delete();
        return [
            'success'   => true,
            'message' => 'User logged out'
        ];
    }

    public function user(Request $request)
    {
        return Auth::user();
    }
}
