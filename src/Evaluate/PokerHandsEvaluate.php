<?php
declare(strict_types=1);

namespace Rsaweb\Poker\Evaluate;

use Rsaweb\Poker\Contracts\Suite;
use function array_map;
use function in_array;

final class PokerHandsEvaluate
{
    /**
     * @var Suite[]
     */
    private array $suites;

    /**
     * A map of the suite values and the number of times they appear in the hand
     *
     * @var array<string, int>
     */
    private array $suiteCount;

    public function __construct(Suite ...$suites)
    {
        $this->suites = $suites;

        $this->suiteCount = array_count_values(
            array_map(
                static fn (Suite $suite) => $suite->value,
                $suites
            )
        );
    }

    public function hasOnePair(): bool
    {
        return in_array(2, $this->suiteCount, true);
    }

    public function hasTwoPair(): bool
    {
        return count(array_keys($this->suiteCount, 2, true)) === 2;
    }

    public function hasThreeOfAKind(): bool
    {
        return in_array(3, $this->suiteCount, true);
    }

    public function hasFourOfAKind(): bool
    {
        return in_array(4, $this->suiteCount, true);
    }

    public function hasFullHouse(): bool
    {
        return $this->hasOnePair() && $this->hasThreeOfAKind();
    }

    public function hasStraight(): bool
    {
        $suiteValues = array_map(static fn (Suite $suite) => $suite->value, $this->suites);

        $isStraight = count(array_diff(range(min($suiteValues), max($suiteValues)), $suiteValues)) === 0;

        if  (false === $isStraight && $this->hasAce()) {
            // Handle a straight with an ace, E.G 10, J, Q, K, A
            $suiteValues = array_map(static fn (Suite $suite) => $suite->value === 1 ? 14 : $suite->value, $this->suites);

            return count(array_diff(range(min($suiteValues), max($suiteValues)), $suiteValues)) === 0;
        }

        return $isStraight;
    }

    public function hasStraightFlush(): bool
    {
        return $this->hasStraight() && $this->hasFlush();
    }

    public function hasRoyalFlush(): bool
    {
        if (false === $this->hasStraightFlush()) {
            return false;
        }

        $suiteValues = array_map(static fn (Suite $suite) => $suite->value === 1 ? 14 : $suite->value, $this->suites);

        return $this->hasStraightFlush() && count(array_diff([10, 11, 12, 13, 14], $suiteValues)) === 0;
    }

    public function hasFlush(): bool
    {
        return count(array_unique(array_map(static fn (Suite $suite) => $suite::class, $this->suites))) === 1;
    }

    private function hasAce(): bool
    {
        return in_array(1, array_map(static fn (Suite $suite) => $suite->value, $this->suites), true);
    }
}
