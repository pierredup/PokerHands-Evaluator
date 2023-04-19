<?php
declare(strict_types=1);

namespace Rsaweb\Poker\Exception;

use RuntimeException;
use Throwable;

final class NonUniqueCardsException extends RuntimeException
{
    public function __construct(array $duplicateCards, int $code = 0, Throwable $previous = null)
    {
        $message = sprintf('The following cards are duplicated: %s', implode(', ', $duplicateCards));

        parent::__construct($message, $code, $previous);
    }
}
