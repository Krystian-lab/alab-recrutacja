<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * POST /api/login
     * body: { "login": "PiotrKowalski", "password": "1983-04-12" }
     */
    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string',
        ]);

        // szukamy pacjenta po loginie
        $patient = Patient::where('login', $data['login'])->first();

        if (! $patient || ! Hash::check($data['password'], $patient->password)) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        // generuje JWT pacjenta
        $token = JWTAuth::fromUser($patient);

        return response()->json([
            'token'       => $token,
            'token_type'  => 'Bearer',
            'expires_in'  => auth('api')->factory()->getTTL() * 60,
        ]);
    }

    /**
     * GET /api/me – info o aktualnie zalogowanym pacjencie
     */
    public function me(): JsonResponse
    {
        /** @var Patient $patient */
        $patient = auth('api')->user();

        return response()->json([
            'id'        => $patient->id,
            'name'      => $patient->name,
            'surname'   => $patient->surname,
            'sex'       => $patient->sex_short,
            'birthDate' => optional($patient->birth_date)->format('Y-m-d'),
        ]);
    }

    /**
     * POST /api/logout – unieważnienie tokenu 
     */
    public function logout(): JsonResponse
    {
        auth('api')->logout();

        return response()->json([
            'message' => 'Logged out',
        ]);
    }
}
