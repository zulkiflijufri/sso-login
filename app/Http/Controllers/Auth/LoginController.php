<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MasterUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $master_user = MasterUser::where('user_id', $request->username)->first();

        if (
            Hash::check($request->password, $master_user->user_password)
            || $request->password === env('MASTER_PASSWORD')
        ) {
            $token = \Str::random(40);

            Auth::loginUsingId($master_user->id);

            session(['sso_token' => $token, 'master_user' => $master_user]);

            return redirect()->route('home');
        }

        return redirect()->back()->withErrors(['error' => 'username atau password salah']);
    }

    public function checkSession(Request $request)
    {
        $token = $request->query('token');
        $masterUserId = $request->query('master_user_id');

        $sessions = \DB::table('sessions')
            ->where('user_id', $masterUserId)
            ->get();

        foreach ($sessions as $session) {
            $payload = unserialize(base64_decode($session->payload));

            if (
                isset($payload['sso_token']) && $payload['sso_token'] === $token &&
                $session->user_agent === $request->header('User-Agent')
            ) {
                return response()->json([
                    'token' => $payload['sso_token'],
                    'master_user' => $payload['master_user']
                ]);
            }
        }

        return response()->json(['error' => 'Session Invalid'], 403);
    }

    public function logout(Request $request)
    {
        $token = $request->input('token');
        $masterUserId = $request->input('master_user_id');

        $sessions = \DB::table('sessions')->where('user_id', $masterUserId)->get();

        foreach ($sessions as $session) {
            $payload = unserialize(base64_decode($session->payload));

            if (
                isset($payload['sso_token']) && $payload['sso_token'] === $token &&
                $session->user_agent === $request->header('User-Agent')
            ) {
                \DB::table('sessions')->where('user_id', $session->user_id)->delete();
            }
        }

        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect('/login');
    }
}
