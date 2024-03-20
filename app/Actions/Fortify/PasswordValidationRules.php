<?php

namespace OGame\Actions\Fortify;

use Illuminate\Validation\Rules\Password;

trait PasswordValidationRules
{
    /**
     * Get the validation rules used to validate passwords.
     *
     * @return array<int, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    protected function passwordRules(): array
    {
        // Removed: "confirmed" as default register screen does not support a confirm password field as of yet.
        return ['required', 'string', Password::default()];
    }
}
