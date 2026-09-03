<?php

namespace App\Services\Ai;

final class ChatPrompt
{
    public function __construct(
        public readonly string $system,
        public readonly string $user,
    ) {}
}
