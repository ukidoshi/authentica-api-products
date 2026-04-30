<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    #[OA\Post(
        path: '/api/login',
        operationId: 'auth.login',
        description: 'Возвращает Bearer token',
        summary: 'Login',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/LoginPayload'),
        ),
        tags: ['Auth'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Bearer token.',
                content: new OA\JsonContent(ref: '#/components/schemas/AuthToken'),
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error.',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationError'),
            ),
        ],
    )]
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        $user = User::query()
            ->where('email', $credentials['email'])
            ->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $plainToken = Str::random(80);

        $user->forceFill([
            'api_token' => hash('sha256', $plainToken),
        ])->save();

        return response()->json([
            'token_type' => 'Bearer',
            'access_token' => $plainToken,
        ]);
    }

    #[OA\Post(
        path: '/api/logout',
        operationId: 'auth.logout',
        description: 'Удаление текущего токена',
        summary: 'Logout',
        security: [['bearerAuth' => []]],
        tags: ['Auth'],
        responses: [
            new OA\Response(
                response: 204,
                description: 'Logged out.',
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated.',
            ),
        ],
    )]
    public function logout(Request $request): Response
    {
        $request->user()->forceFill([
            'api_token' => null,
        ])->save();

        return response()->noContent();
    }
}
