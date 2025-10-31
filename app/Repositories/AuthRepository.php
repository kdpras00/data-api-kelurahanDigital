<?php
namespace App\Repositories;

use App\Http\Resources\HeadOfFamilyResource;
use App\Interfaces\AuthRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class AuthRepository implements AuthRepositoryInterface
{
    public function login(array $data)
    {
        if (! Auth::guard('web')->attempt($data, true)) {
            return response([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        // Get user ID before regenerating session
        $userId = Auth::id();

        // Regenerate session to prevent session fixation attacks
        request()->session()->regenerate();

        // Re-login user after session regeneration to maintain auth state
        Auth::guard('web')->loginUsingId($userId, true);

        // Force save the session to ensure it's written before response
        request()->session()->save();

        // Get authenticated user with roles and permissions
        $user = Auth::user();
        $user->load('roles.permissions');
        $permissions = $user->roles->flatMap->permissions->pluck('name')->toArray();
        $role = $user->roles->first() ? $user->roles->first()->name : null;

        return response()->json([
            'success' => true,
            'message' => 'Login Sukses',
            'data' => [
                'id'             => $user->id,
                'name'           => $user->name,
                'email'          => $user->email,
                'profile_picture' => $user->profile_picture ? asset('storage/' . $user->profile_picture) : null,
                'identity_number' => $user->identity_number,
                'date_of_birth'   => $user->date_of_birth,
                'permissions'    => $permissions,
                'role'           => $role,
                'head_of_family' => $user->headOfFamily ? new HeadOfFamilyResource($user->headOfFamily) : null,
            ],
        ]);
    }

    public function logout()
    {
        Auth::guard('web')->logout();

        // Invalidate the session and regenerate CSRF token
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        $response = [
            'success' => true,
            'message' => 'Logout Success',
        ];

        return response($response, 200);
    }

    public function me()
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Load roles and permissions if using Spatie Permission
            $user->load('roles.permissions', 'headOfFamily');

            $permissions = $user->roles->flatMap->permissions->pluck('name')->toArray();

            $role = $user->roles->first() ? $user->roles->first()->name : null;

            return response()->json([
                'message' => 'User Data',
                'data'    => [
                    'id'             => $user->id,
                    'name'           => $user->name,
                    'email'          => $user->email,
                    'profile_picture' => $user->profile_picture ? asset('storage/' . $user->profile_picture) : null,
                    'identity_number' => $user->identity_number,
                    'date_of_birth'   => $user->date_of_birth,
                    'permissions'    => $permissions,
                    'role'           => $role,
                    'head_of_family' => $user->headOfFamily ? new HeadOfFamilyResource($user->headOfFamily) : null,
                ],
            ]);
        }

        return response()->json([
            'message' => 'You are not logged in',
        ], 401);
    }
}
