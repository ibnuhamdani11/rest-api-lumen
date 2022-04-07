<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Users;

class AuthController extends Controller
{


    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'refresh', 'logout']]);
    }
    /**
     * Get a JWT via given credentials.
     *
     * @param  Request  $request
     * @return Response
     */
    // public function login(Request $request)
    // {

    //     $this->validate($request, [
    //         'email' => 'required|string',
    //         'password' => 'required|string',
    //     ]);

    //     $credentials = $request->only(['email', 'password']);

    //     if (! $token = Auth::attempt($credentials)) {
    //         return response()->json(['message' => 'Unauthorized'], 401);
    //     }

    //     return $this->respondWithToken($token);
    // }

    public function login(Request $request)
    {
        $email = $request->email;
        $password  = $request->password;

        if(empty($email) || empty($password)){
            return response()->json(['status'=>'error','message'=>'You must fill all the fields'], 400);
        }

        $credentials = array(
            "email"=> $email,
            "password"=> md5($password)
        );

        $user = Users::where('email', $email)->where('password', md5($password))
                    ->first();
                    
        if($user){ 
            if (! $token =Auth::login($user)) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            return $this->respondWithToken($token, $user);
        }else{
            return response()->json(['status'=>'failed','message'=>'Login failed, please check email and password again','code'=>200]);
        }

    }

    protected function respondWithToken($token, $user)
    {
        return response()->json([
            'code' => 200, 
            'data' => array(
                            'data_user' => $user,
                            'access_token' => $token, 
                            'token_type' => 'bearer',
                            'expires_in' => auth()->factory()->getTTL() * 60                    
                            ),
        ]);
    }

     /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    // protected function respondWithToken($token)
    // {
    //     return response()->json([
    //         'access_token' => $token,
    //         'token_type' => 'bearer',
    //         'user' => auth()->user(),
    //         'expires_in' => auth()->factory()->getTTL() * 60 * 24
    //     ]);
    // }
}