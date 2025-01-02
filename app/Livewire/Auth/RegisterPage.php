<?php

namespace App\Livewire\Auth;

use Livewire\Component;

class RegisterPage extends Component
{
    public $name;
    public $email;
    public $password;

    public function save()
    {
        dd($this->name, $this->email, $this->password);
    }
    public function render()
    {
        return view('livewire.auth.register-page');
    }
}
