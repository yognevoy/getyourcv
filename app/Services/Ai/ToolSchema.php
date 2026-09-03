<?php

namespace App\Services\Ai;

interface ToolSchema
{
    public function toolName(): string;

    public function toolDefinition(): array;
}
