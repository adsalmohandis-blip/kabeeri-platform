<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Middleware\ResetCustomerSessionForAdminLogin;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class CustomerAuthController extends Controller
{
    public function showLogin(): View
    {
        return view('customer.v16-auth', [
            'mode' => 'login',
            'paths' => config('kabeeri_customer.audience_paths', []),
        ]);
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, (bool) $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => __('kabeeri.ui.invalid_login')])
                ->onlyInput('email');
        }

        $request->session()->regenerate();
        $request->session()->put(ResetCustomerSessionForAdminLogin::CUSTOMER_AUTH_SURFACE, 'customer');

        return redirect()->intended($this->postAuthTarget($request->user()));
    }

    public function showRegister(Request $request): View
    {
        return view('customer.v16-auth', [
            'mode' => 'register',
            'selectedPath' => $request->query('path', 'business_owner'),
            'paths' => config('kabeeri_customer.audience_paths', []),
        ]);
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'customer_path' => ['nullable', 'string'],
        ]);

        $user = User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);

        UserProfile::query()->create([
            'user_id' => $user->id,
            'visibility' => 'private',
            'metadata' => [
                'source' => 'v16_customer_register',
                'customer_path' => $validated['customer_path'] ?? 'business_owner',
                'capabilities' => ['customer_owner'],
            ],
        ]);

        Auth::login($user);
        $request->session()->regenerate();
        $request->session()->put(ResetCustomerSessionForAdminLogin::CUSTOMER_AUTH_SURFACE, 'customer');

        return redirect()->route('customer.onboarding', [
            'path' => $validated['customer_path'] ?? 'business_owner',
        ]);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('customer.start')->with('status', __('kabeeri.ui.logged_out'));
    }

    private function postAuthTarget(?User $user): string
    {
        if (! $user) {
            return route('customer.start');
        }

        return $user->ownedOrganizations()->exists()
            ? route('customer.workspace')
            : route('customer.onboarding');
    }
}
