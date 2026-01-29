<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Requests\RegisterUserRequest;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function store(RegisterUserRequest $request)
    {
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->role = User::ROLE_USER;
        $user->save();

        return response()->json([
            'success' => true,
            'data' => $user,
            'message' => 'User registered successfully'
        ], 201);
    }

    public function index()
    {
        $users = User::all();

        return response()->json([
            'success' => true,
            'data' => $users,
            'message' => 'Users retrieved successfully'
        ]);
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'You cannot delete your own account'
            ], 403);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'data' => null,
            'message' => 'User deleted successfully'
        ]);
    }
}
