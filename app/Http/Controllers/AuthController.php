<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;

class AuthController extends Controller
{
   public function register(RegisterRequest $request)
   {
    $validatedData=$request->validated();
    $user=User::create([
        'name'=> $validatedData['name'] ,
        'email'=> $validatedData['email'] ,
        'password'=>Hash::make($validatedData['password']) ,
    ]);

$data['token'] = $user->createToken('User')->plainTextToken;
$data['name']=$user['name'];
$data['email']=$user['email'];
    return ApiResponse::sendResponse(201,'User Registered',$data);
   }
   public function login(LoginRequest $request)
   { 
    $validated =  $request ->validated() ;
    $user = User::where('email',$validated['email'])->first();
    if(!$user)
    {
        return ApiResponse::sendResponse(404,'Email Not Found',[]);
    }
    
    if(!Hash::check($validated['password'],$user->password))
    {
        return ApiResponse::sendResponse(404,'Incorect Paswword',[]);
    }
    $data['token']= $user->createToken('UserLogin')->plainTextToken ; 
    $data['name']=$user['name'];
    $data['email']=$user['email'];
    return ApiResponse::sendResponse(200,'User Logging in',$data);
   }
//    public function logout(Request $request)
//    {
//     $token = $request->user()?->currentAccessToken();
//      if($token)
//      {
//         $token->delete();
//         return ApiResponse::sendResponse(200, 'Logged Out Successfully', []);
//      }

//      return ApiResponse::sendResponse(400, 'No active session found', []);

//    }
 public function logout(Request $request)
    {
    $request->user()->currentAccessToken()->delete();
    return ApiResponse::sendResponse(200,'LOgout succsesfully', null);
    }


}
