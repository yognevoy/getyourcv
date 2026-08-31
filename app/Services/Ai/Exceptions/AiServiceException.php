<?php

namespace App\Services\Ai\Exceptions;

use RuntimeException;

/**
 * Thrown for any recoverable AI provider failure (timeout, rate limit,
 * malformed response).
 */
class AiServiceException extends RuntimeException
{
}
