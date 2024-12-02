<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', User::class);

        $users = User::all();
        return response()->json($users);
    }


    /**
     * Display the specified resource.
     */
    public function showUser()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Felhasználó nem található'], 404);
        }

        return response()->json($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request)
    {
        $user = User::find(Auth::user()->id);

        if (!$user) {
            return response()->json(['message' => 'Felhasználó nem található'], 404);
        }

        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->update($request->only(
            'name',
            'email',
            'phoneNumber',
            'address',
            'password'
        ));

        return response()->json($user, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy()
    {
        $user = User::find(Auth::user()->id);

        if (!$user) {
            return response()->json(['message' => 'Felhasználó nem található'], 404);
        }

        if ($user->delete()) {
            return response()->json(['message' => 'Sikeresen törölve!'], 200);
        } else {
            return response()->json(['message' => 'Nem sikerült törölni a felhasználót. Kérlek, próbáld újra később.'], 500);
        }
    }
}
