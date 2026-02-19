<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Hub;
use App\Models\Access;
use App\Enums\AccessRoleEnum;
use \Illuminate\Validation\Rules\Enum;
use Illuminate\Support\Facades\Log;
class UserController extends Controller
{
    
    public function index(Request $request){
        $page_size = $request->query('page_size', 20);
        $search = $request->query('search', null);
        $users = User::when($search, function($query, $search){
            $query->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
        })->paginate($page_size);
        Log::info('Fetched users: ', ['users' => $users]);
        return response()->json($users);
    }

    public function show($id){
        $user = User::find($id);
        if(!$user){
            return response()->json(['message' => 'User not found'], 404);
        }
        return response()->json($user);
    }
    public function create(Request $request){
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'lastmile_role' => ['nullable', new Enum(AccessRoleEnum::class)],
            'transport_role' => ['nullable', new Enum(AccessRoleEnum::class)],

        ]);

        $user = User::create($validated);
        if($user){
            // Create a default access record for the new user
            Access::create(['user_id' => $user->id, 'lastmile_role' => $validated['lastmile_role'], 'transport_role' => $validated['transport_role']]);
        }
        return response()->json($user, 201);
    }
}
