<?php
declare(strict_types=1);

namespace Rsaweb\Poker\Validator;

use Rsaweb\Poker\Contracts\Card;
use Rsaweb\Poker\Enum\Club;
use Rsaweb\Poker\Enum\Diamond;
use Rsaweb\Poker\Enum\Heart;
use Rsaweb\Poker\Enum\Spade;
use Rsaweb\Poker\Exception\InvalidCardException;
use Rsaweb\Poker\Exception\InvalidNumberOfCards;
use Rsaweb\Poker\Exception\NonUniqueCardsException;
use function array_count_values;
use function array_diff;
use function array_filter;
use function array_intersect;
use function array_keys;
use function array_map;
use function array_merge;
use function array_unique;
use function count;
use function current;
use function is_string;

/**
 * Validates that a poker hand is valid.
 *
 * E.G:
 * - A poker hand must have 5 cards
 * - The cards cannot be duplicated
 * - The cards must be valid
 */
final class PokerHandValidator
{
    public const MAX_CARDS = 5;

    /**
     * @throws InvalidCardException|NonUniqueCardsException
     */
    public static function validate(Card|string ...$cards): void
    {
        $allCards = array_merge(
            Spade::cases(),
            Diamond::cases(),
            Heart::cases(),
            Club::cases(),
        );

        $cardKeys = array_map(static fn(Card $card) => $card->toShortString(), $allCards);

        $totalCardCount = count($cards);

        if (is_string(current($cards)) && count(array_intersect($cards, $cardKeys)) !== $totalCardCount) {
            // Get list of all invalid cards
            $invalidCards = array_diff($cards, $cardKeys);

            throw new InvalidCardException($invalidCards);
        }

        if (count(array_unique($cards)) !== $totalCardCount) {
            $duplicateCards = array_keys(
                array_filter(
                    array_count_values($cards),
                    static fn(int $value) => $value > 1
                )
            );

            throw new NonUniqueCardsException($duplicateCards);
        }

        if ($totalCardCount !== self::MAX_CARDS) {
            throw new InvalidNumberOfCards(self::MAX_CARDS, $totalCardCount);
        }
    }
}
