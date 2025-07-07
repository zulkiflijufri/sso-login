<?php

namespace App\Http\Controllers\Auth;

use App\Models\SsoToken;
use App\Models\MasterUser;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $master_user = MasterUser::query()
            ->where('user_id', $request->username)
            ->where('user_enable', TRUE)
            ->first();

        if (
            Hash::check($request->password, $master_user->user_password)
            || $request->password === env('MASTER_PASSWORD')
        ) {
            $existingToken = SsoToken::query()
                ->where('user_id', $master_user->user_id)
                ->where('expires_at', '>', now())
                ->first();

            if ($existingToken) {
                $token = $existingToken->token;
            } else {
                $token = Str::random(40);

                SsoToken::create([
                    'token' => $token,
                    'user_id' => $master_user->user_id,
                    'expires_at' => now()->addMinutes(30),
                ]);
            }

            Auth::loginUsingId($master_user->id);

            return redirect()->route('home');
        }

        return redirect()->back()->withErrors(['error' => 'username atau password salah']);
    }

    public function checkSession(Request $request)
    {
        $token = $request->query('token');

        $ssoToken = SsoToken::query()
            ->with('master_user')
            ->where('token', $token)
            ->where('expires_at', '>', now())
            ->first();

        if (!$ssoToken) {
            return response()->json(['error' => 'Session invalid atau expired.'], 403);
        }

        return response()->json([
            'token' => $ssoToken->token,
            'master_user' => $ssoToken->master_user,
        ]);
    }

    public function logout(Request $request)
    {
        $token = $request->input('token');

        SsoToken::query()
            ->where('token', $token)
            ->delete();

        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect('/logout');
    }
}
