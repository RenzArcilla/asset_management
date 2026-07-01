<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

class Register extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ];
    }

    protected function messages(): array
    {
        return [
            'email.unique' => 'An account with this email already exists.',
        ];
    }

    public function register(): void
    {
        $validated = $this->validate();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Module A: First registered user becomes Admin.
        // All subsequent registrations default to Customer.
        $user->assignRole(
            User::query()->count() === 1 ? 'admin' : 'customer'
        );

        event(new Registered($user));

        Auth::login($user);

        $this->redirect(
            $user->hasRole('admin') ? route('admin.dashboard') : route('dashboard'),
            navigate: true
        );
    }

    public function render()
    {
        return view('livewire.auth.register')
            ->layout('layouts.guest');
    }
}