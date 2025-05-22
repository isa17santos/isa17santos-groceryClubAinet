<?php

namespace App\Livewire\Settings;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class ChangePassword extends Component
{
    public $current_password;
    public $new_password;
    public $new_password_confirmation;
    public $successMessage = null;
    public $errorMessage = null;

    public function changePassword()
    {
        $this->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed|different:current_password',
        ]);

        if (!Hash::check($this->current_password, Auth::user()->password)) {
            $this->errorMessage = 'Current password is incorrect.';
            return;
        }

        Auth::user()->update([
            'password' => bcrypt($this->new_password),
        ]);

        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
        $this->successMessage = 'Password updated successfully.';
    }

    public function render()
    {
        return view('livewire.settings.change-password')->layout('layouts.app');
    }
}
