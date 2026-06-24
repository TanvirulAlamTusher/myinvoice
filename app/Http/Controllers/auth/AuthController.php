<?php
namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Show login page
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Show register page
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $request->validate([
            'login'    => 'required',
            'password' => 'required',
        ]);

        $loginInput = trim($request->login);

        // Detect email or phone
        if (filter_var($loginInput, FILTER_VALIDATE_EMAIL)) {
            $field = 'email';
        } else {
            $field = 'phone';
        }

        $user = User::where($field, $loginInput)->first();

        if (! $user) {
            return back()->withErrors([
                'login' => 'User not found.',
            ])->onlyInput('login');
        }

        $credentials = [
            $field     => $loginInput,
            'password' => $request->password,
        ];

        $remember = $request->filled('remember');

        if (Auth::attempt($credentials, $remember)) {

            $request->session()->regenerate();

            return redirect('/dashboard');
        }

        return back()->withErrors([
            'login' => 'Invalid email/phone or password.',
        ])->onlyInput('login');
    }
    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    /**
     * Handle registration (optional)
     */
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'phone'    => 'required|string|unique:users,phone',
            'email'    => 'nullable|email|unique:users,email',
            'password' => 'required|min:4|confirmed',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'phone'    => $request->phone,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Auto login after registration
        Auth::login($user);

        $request->session()->regenerate();

        return redirect('/dashboard');
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
        ]);

        $loginInput = trim($request->login);
        $field = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        $user = User::where($field, $loginInput)->first();

        if (! $user) {
            return back()->withErrors([
                'login' => 'User not found.',
            ])->onlyInput('login');
        }

        $user->update([
            'password' => Hash::make('1234'),
        ]);

        return back()->with('success', 'Password reset successfully. New password is 1234.');
    }
}
