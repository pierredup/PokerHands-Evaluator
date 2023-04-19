<?php
declare(strict_types=1);

namespace Rsaweb\Poker\Exception;

use RuntimeException;
use Throwable;

final class InvalidCardException extends RuntimeException
{
    public function __construct(array $invalidCards, int $code = 0, Throwable $previous = null)
    {
        $message = sprintf('The following cards are invalid: %s', implode(', ', $invalidCards));

        parent::__construct($message, $code, $previous);
    }
}
