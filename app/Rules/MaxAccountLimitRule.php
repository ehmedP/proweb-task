<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\User;

class MaxAccountLimitRule implements ValidationRule
{
    public function __construct(
        protected int $maxAllowed = 5
    ) { }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $user = User::query()->find($value);

        if (!$user) {
            $fail("The selected user does not exist.");
            return;
        }

        if ($user->accounts()->count() >= $this->maxAllowed) {
            $fail("Maximum account limit exceeded. You can have a maximum of {$this->maxAllowed} accounts.");
        }
    }
}
