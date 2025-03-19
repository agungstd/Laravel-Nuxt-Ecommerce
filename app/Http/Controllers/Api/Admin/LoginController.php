<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Cache\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{    
    /**
     * index
     *
     * @param  mixed $request
     * @return void
     */
    public function index(Request $request)
    {
        // Implement rate limiting for login attempts
        $key = Str::lower($request->input('email').'|'.$request->ip());
        $limiter = app(RateLimiter::class);
        
        if ($limiter->tooManyAttempts($key, 5)) {
            $seconds = $limiter->availableIn($key);
            
            return response()->json([
                'success' => false,
                'message' => 'Too many login attempts. Please try again after '.$seconds.' seconds.'
            ], 429);
        }
        
        //set validasi
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|min:6',
        ]);
        
        //response error validasi
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //get "email" dan "password" dari input
        $credentials = $request->only('email', 'password');

        try {
            //check jika "email" dan "password" tidak sesuai
            if(!$token = auth()->guard('api_admin')->attempt($credentials)) {
                // Increment failed login attempts
                $limiter->hit($key, 60);
                
                //response login "failed"
                return response()->json([
                    'success' => false,
                    'message' => 'Email or Password is incorrect'
                ], 401);
            }
            
            // Reset rate limiter on successful login
            $limiter->clear($key);
            
            // Set token expiration
            $tokenExpiration = auth()->guard('api_admin')->factory()->getTTL() * 60;
            
            //response login "success" dengan generate "Token"
            return response()->json([
                'success' => true,
                'user'    => auth()->guard('api_admin')->user(),  
                'token'   => $token,
                'token_type' => 'bearer',
                'expires_in' => $tokenExpiration
            ], 200);
            
        } catch (\Exception $e) {
            Log::error('Login error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during authentication'
            ], 500);
        }
    }
    
    /**
     * getUser
     *
     * @return void
     */
    public function getUser()
    {
        try {
            //response data "user" yang sedang login
            return response()->json([
                'success' => true,
                'user'    => auth()->guard('api_admin')->user()
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User data could not be retrieved'
            ], 401);
        }
    }
    
    /**
     * refreshToken
     *
     * @param  mixed $request
     * @return void
     */
    public function refreshToken(Request $request)
    {
        try {
            //refresh "token"
            $refreshToken = JWTAuth::refresh(JWTAuth::getToken());
    
            //set user dengan "token" baru
            $user = JWTAuth::setToken($refreshToken)->toUser();
    
            //set header "Authorization" dengan type Bearer + "token" baru
            $request->headers->set('Authorization','Bearer '.$refreshToken);
    
            // Set token expiration
            $tokenExpiration = auth()->guard('api_admin')->factory()->getTTL() * 60;
            
            //response data "user" dengan "token" baru
            return response()->json([
                'success' => true,
                'user'    => $user,
                'token'   => $refreshToken,
                'token_type' => 'bearer',
                'expires_in' => $tokenExpiration
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token could not be refreshed'
            ], 401);
        }
    }
    
    /**
     * logout
     *
     * @return void
     */
    public function logout()
    {
        try {
            //remove "token" JWT
            JWTAuth::invalidate(JWTAuth::getToken());
    
            //response "success" logout
            return response()->json([
                'success' => true,
                'message' => 'Successfully logged out'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to logout'
            ], 500);
        }
    }
}