<?php
namespace App\Service;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\User;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class AuthContainer implements AuthContract{
    
    public function register($request){
        $data = $request->all();
        $validate = Validator::make($data,[
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string'
        ]);
        if ($validate->fails()) {
            return response()->json(['message' => $validate->messages()], 400);
        }
        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);
        $user->save();
        return response()->json([
            'message' => 'Successfully created user!'
        ], 200);
    }
    
    public function login($request){
        $credentials = $request->only('email', 'password');
        $token = null;
        
        try {
                if (! $token = JWTAuth::attempt($credentials)) {
                    return response()->json(['error' => 'invalid_credentials'], 400);
                }
            } catch (JWTException $e) {
                return response()->json(['error' => 'could_not_create_token'], 500);
            }
//        return response()->json(compact('token'));
        return response()->json([
            'message' => 'Successfully Login',
            'Authorization' => 'Bearer ' . $token
        ], 200);
    }
    
    public function edit($request){
        $data = $request->all();
        $validate = Validator::make($data,[
            'name' => 'required',
            'password' => 'required',
        ]);
        if ($validate->fails()) {
            return response()->json(['message' => $validate->messages()], 400);
        }
        $credentials = request(['email', 'password']);
        $user = $request->user();
        $user->name = $data['name'];
        $user->password = bcrypt( $data['password'] );
        $user->save();
        return response()->json(['data' => $user]);
    }
    
    public function delete($request){
        $user = $request->user();
        DB::table('users')->where('id',$user->id)->delete();
        return response()->json([
            'message' => 'Successfully deleted user!' . $user->email
        ], 200);
    }
    
    public function binarySearch(){
        $arr = array(19, 21, 31, 41, 55, 12, 75 ,70,1 , 98); 
        sort($arr);
        $value = 12; 
        $lower = 0; 
        $higher = count($arr) - 1; 
        $result = 0;
        while ($lower <= $higher) { 
            // compute middle index 
            $mid = floor(($lower + $higher) / 2); 
            // element found at mid 
            if($arr[$mid] == $value) { 
                $result = 1; 
            }
            if ($value < $arr[$mid]) { 
                // search the left side of the array 
                $higher = $mid -1; 
            }else { 
                // search the right side of the array 
                $lower = $mid + 1; 
            }
        } 
        
        if( $result == 1) { 
            return $value." Exists"; 
        } 
        return $value." Doesnt Exist"; 
    }
    
}