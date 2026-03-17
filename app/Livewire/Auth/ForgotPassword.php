<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.guest')]
class ForgotPassword extends Component
{
    #[Validate('required|email')]
    public string $email = '';

    public bool $sent = false;

    public function sendResetLink(): void
    {
        $this->validate();

        $status = Password::sendResetLink(['email' => $this->email]);

        if ($status === Password::RESET_LINK_SENT) {
            $this->sent = true;
        } else {
            $this->addError('email', 'Não foi possível enviar o link de recuperação.');
        }
    }

    public function render()
    {
        return view('livewire.auth.forgot-password');
    }
}
