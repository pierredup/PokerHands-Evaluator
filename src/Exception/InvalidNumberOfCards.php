<?php
declare(strict_types=1);

namespace Rsaweb\Poker\Exception;

use RuntimeException;
use Throwable;

final class InvalidNumberOfCards extends RuntimeException
{
    public function __construct(int $expectedNumber, int $actualNumber, int $code = 0, Throwable $previous = null)
    {
        $message = sprintf('Expected %d cards, got %d', $expectedNumber, $actualNumber);

        parent::__construct($message, $code, $previous);
    }
}
