<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\Concerns\RespondsWithJson;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\LoginRequest;
use App\Http\Requests\Api\V1\RegisterRequest;
use App\Models\User;
use App\Services\Api\ApiCartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use RespondsWithJson;

    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::query()
            ->where('email', $request->validated('email'))
            ->first();

        if (! $user || ! Hash::check($request->validated('password'), $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['These credentials do not match our records.'],
            ]);
        }

        if (! $user->is_active) {
            throw ValidationException::withMessages([
                'email' => ['Your account has been deactivated.'],
            ]);
        }

        if ($request->boolean('revoke_other_tokens')) {
            $user->tokens()->delete();
        }

        app(ApiCartService::class)->mergeGuestCartIntoUser($user);

        $deviceName = $request->validated('device_name', 'mobile');
        $token = $user->createToken($deviceName);

        return $this->success([
            'token' => $token->plainTextToken,
            'token_type' => 'Bearer',
            'user' => $user->apiProfilePayload(),
        ]);
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = User::query()->create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'name' => trim($data['first_name'].' '.$data['last_name']),
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => $data['password'],
            'role' => \App\Enums\UserRole::Customer,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        app(ApiCartService::class)->mergeGuestCartIntoUser($user);

        $deviceName = $request->validated('device_name', 'mobile');
        $token = $user->createToken($deviceName);

        return $this->success([
            'token' => $token->plainTextToken,
            'token_type' => 'Bearer',
            'user' => $user->apiProfilePayload(),
        ], status: 201);
    }

    public function me(Request $request): JsonResponse
    {
        return $this->success($request->user()->apiProfilePayload());
    }

    public function refresh(Request $request): JsonResponse
    {
        $user = $request->user();
        $oldToken = $user->currentAccessToken();
        $deviceName = $oldToken->name ?? 'mobile';

        $oldToken->delete();

        $token = $user->createToken($deviceName);

        return $this->success([
            'token' => $token->plainTextToken,
            'token_type' => 'Bearer',
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->success(['message' => 'Logged out successfully.']);
    }
}
