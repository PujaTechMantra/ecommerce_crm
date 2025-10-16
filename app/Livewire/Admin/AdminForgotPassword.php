<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Admin;
use App\Models\AdminPasswordReset;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AdminForgotPassword extends Component
{
    public $step = 1;
    public $mobile, $otp, $password, $password_confirmation;
    protected $listeners = ['timerExpired'];

    public function sendOtp()
    {
        $this->validate([
            'mobile' => 'required|digits:10|exists:admins,mobile',
        ]);

        $otp = rand(1000,9999);
        $expiresAt = Carbon::now()->addMinutes(60);
        $user_type = 'admin';
        // Send OTP to mobile (using a hypothetical sendSms function)
        sendSms($this->mobile, $otp, $user_type);

        AdminPasswordReset::updateOrCreate(
            ['mobile' => $this->mobile],
            ['otp' => $otp, 'expires_at' => $expiresAt]
        );

        $this->step = 2;

        // Dispatch event once after OTP is sent
        $this->dispatch('startTimer');
    }


    public function timerExpired()
    {
        $this->step = 1;
    }

    public function verifyOtp()
    {
        $this->validate([
            'otp' => 'required|digits:4'
        ]);

        $reset = AdminPasswordReset::where('mobile', $this->mobile)
            ->where('otp', $this->otp)
            ->first();
        if (!$reset) {
            $this->addError('otp', 'Invalid OTP.');
            return;
        }

        if (Carbon::now()->gt($reset->expires_at)) {
            $this->addError('otp', 'OTP has expired.');
            return;
        }
        $this->step = 3;
    }

    public function resetPassword()
    {
        $this->validate([
            'password' => 'required|min:6|confirmed'
        ]);

        $admin = Admin::where('mobile', $this->mobile)->first();
        $admin->password = Hash::make($this->password);
        $admin->save();

        AdminPasswordReset::where('mobile', $this->mobile)->delete();

        session()->flash('success', 'Password reset successfully.');
        return redirect()->route('login');
    }

    public function render()
    {
        return view('livewire.admin.admin-forgot-password')->layout('components.layouts.admin');
    }
}
