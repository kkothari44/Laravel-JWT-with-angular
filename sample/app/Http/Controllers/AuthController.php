<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\User;
use Illuminate\Support\Facades\Validator;
use App\Service\AuthContract;
use Tymon\JWTAuth\Facades\JWTAuth;
class AuthController extends Controller
{
    /**
     * Create user
     *
     * @param  [string] name
     * @param  [string] email
     * @param  [string] password
     * @param  [string] password_confirmation
     * @return [string] message
     */
    public function signup(Request $request, AuthContract $authContract)
    {
        return $authContract->register($request);
    }
  
    /**
     * Login user and create token
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [boolean] remember_me
     * @return [string] access_token
     * @return [string] token_type
     * @return [string] expires_at
     */
    public function login(Request $request, AuthContract $authContract)
    {
        return $authContract->login($request);
    }
  
    /**
     * Logout user (Revoke the token)
     *
     * @return [string] message
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }
  
    /**
     * Get the authenticated User
     *
     * @return [json] user object
     */
    public function user(Request $request)
    {
        $user = JWTAuth::toUser($request->token);
        $phone = User::find($user->id)->phone;

        return response()->json(['data' => $user,'phone' => $phone]);
    }
    
    public function edit(Request $request,AuthContract $authContract)
    {
        return $authContract->edit($request);
    }
    
    public function delete(Request $request,AuthContract $authContract)
    {
        return $authContract->delete($request);
    }
    
    public function binarySearch(AuthContract $authContract)
    {
        return $authContract->binarySearch();
    }
}