<?php
declare(strict_types=1);

namespace Rsaweb\Poker\Validator;

use Rsaweb\Poker\Contracts\Suite;
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
    public static function validate(Suite|string ...$suites): void
    {
        $allSuites = array_merge(
            Spade::cases(),
            Diamond::cases(),
            Heart::cases(),
            Club::cases(),
        );

        $suiteKeys = array_map(static fn(Suite $suite) => $suite->toShortString(), $allSuites);

        $totalSuiteCount = count($suites);

        if (is_string(current($suites)) && count(array_intersect($suites, $suiteKeys)) !== $totalSuiteCount) {
            // Get list of all invalid cards
            $invalidCards = array_diff($suites, $suiteKeys);

            throw new InvalidCardException($invalidCards);
        }

        if (count(array_unique($suites)) !== $totalSuiteCount) {
            $duplicateCards = array_keys(
                array_filter(
                    array_count_values($suites),
                    static fn(int $value) => $value > 1
                )
            );

            throw new NonUniqueCardsException($duplicateCards);
        }

        if ($totalSuiteCount !== self::MAX_CARDS) {
            throw new InvalidNumberOfCards(self::MAX_CARDS, $totalSuiteCount);
        }
    }
}
