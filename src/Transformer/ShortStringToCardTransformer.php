<?php
declare(strict_types=1);

namespace Rsaweb\Poker\Transformer;

use Rsaweb\Poker\Contracts\Card;
use Rsaweb\Poker\Enum\Club;
use Rsaweb\Poker\Enum\Diamond;
use Rsaweb\Poker\Enum\Heart;
use Rsaweb\Poker\Enum\Spade;
use Rsaweb\Poker\Exception\InvalidCardException;
use function array_combine;
use function array_map;
use function array_merge;
use function in_array;

final class ShortStringToCardTransformer implements TransformerInterface
{
    /**
     * A map of all the cards converted to string with the short notation as the key
     * E.G. ['2H' => 'Two of Hearts']
     *
     * @var array<string, Card>
     */
    private array $allCards;

    /**
     * A list of all the cards in short notation
     *
     * @var list<string>
     */
    private array $cardKeys;

    public function __construct()
    {
        $cards = array_merge(
            Spade::cases(),
            Diamond::cases(),
            Heart::cases(),
            Club::cases(),
        );

        $this->allCards = array_combine(
            array_map(static fn(Card $card) => $card->toShortString(), $cards),
            $cards
        );

        $this->cardKeys = array_keys($this->allCards);
    }

    /**
     * @throws InvalidCardException
     */
    public function transform(string $card): Card
    {
        if (!in_array($card, $this->cardKeys, true)) {
            throw new InvalidCardException([$card]);
        }

        return $this->allCards[$card];
    }

    /**
     * @param list<string> $cards
     * @return list<Card>
     *
     * @throws InvalidCardException
     */
    public function transformArray(array $cards): array
    {
        return array_map($this->transform(...), $cards);
    }
}
