<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class AdminLogin extends Component
{
    public $login_id = '';
    public $password = '';

    public function mount()
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }
        if (Auth::guard('organization')->check()) {
            return redirect()->route('organization.dashboard');
        }
    }
    public function login()
    {
        $this->validate([
            'login_id' => 'required', // can be email OR mobile
            'password' => 'required|string',
        ]);

        $credentials = ['password' => $this->password];

        // Check if input is email or mobile
        if (filter_var($this->login_id, FILTER_VALIDATE_EMAIL)) {
            $credentials['email'] = $this->login_id;
        } else {
            $credentials['mobile'] = $this->login_id;
        }

        // Try Admin Login
        if (Auth::guard('admin')->attempt($credentials)) {
            session()->regenerate();
            return redirect()->route('admin.dashboard');
        }

        // Try Organization Login
        if (Auth::guard('organization')->attempt($credentials)) {
            $organization = Auth::guard('organization')->user();

            // Check if organization is active
            if ($organization->status == 0) {
                Auth::guard('organization')->logout();
                session()->invalidate();
                session()->regenerateToken();
                session()->flash('message', 'Your account is inactive. Please contact administration.');
                return redirect()->route('login');
            }

            session()->regenerate();
            return redirect()->route('organization.dashboard');
        }

        // If both fail
        session()->flash('message', 'Invalid credentials!');
    }


    // Unified logout method
    public function logout()
    {
        // Check if admin is logged in
        if (Auth::guard('admin')->check()) {
            Auth::guard('admin')->logout();
        }

        // Check if organization is logged in
        if (Auth::guard('organization')->check()) {
            Auth::guard('organization')->logout();
        }

        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('login');
    }

    public function render()
    {
        return view('livewire.admin-login')->layout('components.layouts.admin');
    }
}
