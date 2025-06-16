<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Carbon\Carbon;

class UserData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
        public array $roles,
        public Carbon $created_at,
        public ?Carbon $updated_at = null,
    ) {}

}
