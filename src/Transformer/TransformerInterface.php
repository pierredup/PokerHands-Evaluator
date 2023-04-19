<?php
declare(strict_types=1);

namespace Rsaweb\Poker\Transformer;

use Rsaweb\Poker\Contracts\Card;
use Rsaweb\Poker\Exception\InvalidCardException;

interface TransformerInterface
{
    /**
     * @throws InvalidCardException
     */
    public function transform(string $card): Card;

    /**
     * @param list<string> $cards
     * @return list<Card>
     *
     * @throws InvalidCardException
     */
    public function transformArray(array $cards): array;
}
