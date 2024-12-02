<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\RateLimiter;

class AuthController extends Controller
{

    public function login(LoginUserRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user(); // Az aktuális autentikált felhasználó
            $token = $user->createToken('API token')->plainTextToken;

            return response()->json([
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ]
            ], 200);
        }

        return response()->json([
            'error' => [
                'message' => 'Hibás email vagy jelszó!',
                'code' => 401,
            ]
        ], 401);
    }

    public function register(StoreUserRequest $request)
    {
        $ipAddress = $request->ip();
        if (RateLimiter::tooManyAttempts('register:' . $ipAddress, 5)) {
            return response()->json([
                'message' => 'Túl sok regisztrációs próbálkozás. Kérjük, várjon: ' . RateLimiter::availableIn('register:' . $ipAddress) . ' másodpercet.',
            ], 429);
        }

        try {
            RateLimiter::hit('register:' . $ipAddress, 60);

            $validated = $request->validated();

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'phone_number' => $validated['phone_number'] ?? null,
                'address' => $validated['address'] ?? null,
            ]);

            $token = $user->createToken('API token')->plainTextToken;

            return response()->json([
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone_number' => $user->phone_number,
                    'address' => $user->address,
                ],
                'token' => $token,
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Érvénytelen adatok lettek megadva.',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            Log::error('Regisztrációs hiba: ', [
                'hiba_üzenet' => $e->getMessage(),
                'nyomvonal' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'Hiba történt a regisztráció során. Kérjük, próbálja meg később!',
            ], 500);
        }
    }


    public function logout()
    {
        if (Auth::check()) {
            Auth::user()->tokens()->delete();
            return response()->json(["message" => "Sikeres kijelentkezés"], 200);
        }
        return response()->json([
            'error' => [
                'message' => 'Hiba!',
                'code' => 400,
            ]
        ], 401);
    }

    public function checkEmail($email)
    {
        $user = User::where('email', $email)->first();

        if ($user) {
            return response()->json(['exists' => true], 200);
        } else {
            return response()->json(['exists' => false], 200);
        }
    }
}
