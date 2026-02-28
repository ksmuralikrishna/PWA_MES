<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * POST /api/auth/login
     */
    public function login(Request $request)
    {
        $request->validate([
            'login'    => 'required|string',   // accepts email or username
            'password' => 'required|string',
        ]);

        // Find by email or username
        $user = User::where('email', $request->login)
                    ->orWhere('username', $request->login)
                    ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'login' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (!$user->is_active) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Your account has been disabled. Please contact an administrator.',
            ], 403);
        }

        // Update last login timestamp
        $user->update(['last_login_at' => now()]);

        // Revoke old tokens (single session) — remove if multi-device needed
        $user->tokens()->delete();

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'status' => 'ok',
            'data'   => [
                'token'      => $token,
                'token_type' => 'Bearer',
                'user'       => $this->userResource($user),
            ],
        ]);
    }

    /**
     * POST /api/auth/logout
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status'  => 'ok',
            'message' => 'Logged out successfully.',
        ]);
    }

    /**
     * GET /api/auth/me
     * Returns authenticated user + their module permissions
     */
    public function me(Request $request)
    {
        $user = $request->user()->load([
            'modulePermissions.module' => fn($q) => $q->where('is_active', true)->orderBy('sort_order'),
        ]);

        return response()->json([
            'status' => 'ok',
            'data'   => $this->userResource($user),
        ]);
    }

    /**
     * POST /api/auth/refresh
     * Revoke current token and issue a new one
     */
    public function refresh(Request $request)
    {
        $user = $request->user();
        $request->user()->currentAccessToken()->delete();
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'status' => 'ok',
            'data'   => [
                'token'      => $token,
                'token_type' => 'Bearer',
            ],
        ]);
    }

    // ─── Private Helpers ───────────────────────────────────────────

    private function userResource(User $user): array
    {
        $data = [
            'id'            => $user->id,
            'name'          => $user->name,
            'email'         => $user->email,
            'username'      => $user->username,
            'role'          => $user->role,
            'department'    => $user->department,
            'phone'         => $user->phone,
            'is_active'     => $user->is_active,
            'last_login_at' => $user->last_login_at,
        ];

        // For normal users, attach their module permissions
        if ($user->isNormal() && $user->relationLoaded('modulePermissions')) {
            $data['permissions'] = $user->modulePermissions->map(fn($p) => [
                'module'     => $p->module?->slug,
                'module_name'=> $p->module?->name,
                'can_view'   => $p->can_view,
                'can_create' => $p->can_create,
                'can_edit'   => $p->can_edit,
                'can_delete' => $p->can_delete,
            ]);
        }

        // Admin and management get full access flag
        if ($user->isAdmin() || $user->isManagement()) {
            $data['full_access'] = true;
        }

        return $data;
    }
}
