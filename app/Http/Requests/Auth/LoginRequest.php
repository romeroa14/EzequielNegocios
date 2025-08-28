<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['nullable', 'string'], // Permitir nulo para usuarios de Google OAuth
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $email = $this->input('email');
        $password = $this->input('password');

        // Verificar si es un usuario de Google OAuth sin contraseña
        if (empty($password)) {
            $person = \App\Models\Person::where('email', $email)
                ->whereNotNull('google_id')
                ->whereNull('password')
                ->first();

            if ($person) {
                // Usuario de Google OAuth sin contraseña, mostrar mensaje para usar Google
                throw ValidationException::withMessages([
                    'email' => 'Este usuario se registró con Google y no tiene contraseña configurada. Por favor usa el botón "Iniciar sesión con Google" para acceder, o configura una contraseña en tu perfil.',
                ]);
            }
        }

        // Intentar autenticar como persona (comprador/vendedor)
        if (Auth::guard('person')->attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::clear($this->throttleKey());
            return;
        }

        // Si falla, intentar autenticar como usuario admin
        if (Auth::guard('admin')->attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::clear($this->throttleKey());
            return;
        }

        RateLimiter::hit($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.failed'),
        ]);
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    private function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->input('email')).'|'.$this->ip());
    }
} 