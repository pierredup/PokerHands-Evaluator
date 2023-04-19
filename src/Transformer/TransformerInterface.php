<?php
declare(strict_types=1);

namespace Rsaweb\Poker\Transformer;

use Rsaweb\Poker\Contracts\Suite;
use Rsaweb\Poker\Exception\InvalidCardException;

interface TransformerInterface
{
    /**
     * @throws InvalidCardException
     */
    public function transform(string $suite): Suite;
}
