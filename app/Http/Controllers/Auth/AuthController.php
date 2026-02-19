<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Access;
use App\Enums\PositionEnum;

class AuthController extends Controller
{
    public function logout(Request $request){
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    public function googleLogin(Request $request){
        try {
            // Validate incoming request
            $validated = $request->validate([
                'email' => 'required|email',
                'name' => 'required|string',
                'google_id' => 'required|string',
            ]);

            // Create user and (if new) an associated access row (no transaction)
            $user = User::with('access')->firstOrCreate(
                ['email' => $validated['email']],
                [
                    'name' => $validated['name'],
                    'google_id' => $validated['google_id'],
                    'is_active' => true,
                    'position' => PositionEnum::BACKROOM->value,
                ]
            );
            
            // If the user was just created, also create a default access row
            if ($user->wasRecentlyCreated) {
                // Access table has DB defaults for roles; create defaults explicitly if desired
                $access = Access::create([
                    'user_id' => $user->id,
                    
                ]);
                
            }

           
            // Ensure google_id is set for existing users
            if (!$user->google_id && $validated['google_id']) {
                $user->google_id = $validated['google_id'];
                $user->save();
            }
            
            $token = $user->createToken('auth_token')->accessToken;
          
            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user,
                
            ], 200);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            
            return response()->json([
                'message' => 'Login failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
