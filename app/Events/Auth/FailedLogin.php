<?php

namespace App\Events\Auth;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;

class FailedLogin extends Event
{
    use SerializesModels;

    public function __construct(
        public ?string $attempted_login,
        public string $ip,
        public ?string $userAgent = null
    ) {}
}