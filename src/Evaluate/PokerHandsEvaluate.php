<?php
declare(strict_types=1);

namespace Rsaweb\Poker\Evaluate;

use Rsaweb\Poker\Contracts\Card;
use Rsaweb\Poker\Enum\PokerHands;
use function array_map;
use function in_array;

final class PokerHandsEvaluate
{
    /**
     * @var Card[]
     */
    private array $cards;

    /**
     * A map of the card values and the number of times they appear in the hand
     *
     * @var array<string, int>
     */
    private array $cardCount;

    public function __construct(Card ...$card)
    {
        $this->cards = $card;

        $this->cardCount = array_count_values(
            array_map(
                static fn (Card $card) => $card->value,
                $card
            )
        );
    }

    public function hasOnePair(): bool
    {
        return in_array(2, $this->cardCount, true);
    }

    public function hasTwoPair(): bool
    {
        return count(array_keys($this->cardCount, 2, true)) === 2;
    }

    public function hasThreeOfAKind(): bool
    {
        return in_array(3, $this->cardCount, true);
    }

    public function hasFourOfAKind(): bool
    {
        return in_array(4, $this->cardCount, true);
    }

    public function hasFullHouse(): bool
    {
        return $this->hasOnePair() && $this->hasThreeOfAKind();
    }

    public function hasStraight(): bool
    {
        $cardValues = array_map(static fn (Card $card) => $card->value, $this->cards);

        $isStraight = count(array_diff(range(min($cardValues), max($cardValues)), $cardValues)) === 0;

        if  (false === $isStraight && $this->hasAce()) {
            // Handle a straight with an ace, E.G 10, J, Q, K, A
            $cardValues = array_map(static fn (Card $card) => $card->value === 1 ? 14 : $card->value, $this->cards);

            return count(array_diff(range(min($cardValues), max($cardValues)), $cardValues)) === 0;
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

        $cardValues = array_map(static fn (Card $card) => $card->value === 1 ? 14 : $card->value, $this->cards);

        return $this->hasStraightFlush() && count(array_diff([10, 11, 12, 13, 14], $cardValues)) === 0;
    }

    public function hasFlush(): bool
    {
        return count(array_unique(array_map(static fn (Card $card) => $card::class, $this->cards))) === 1;
    }

    public function getHighestRank(): PokerHands
    {
        return match(true) {
            $this->hasRoyalFlush() => PokerHands::RoyalFlush,
            $this->hasStraightFlush() => PokerHands::StraightFlush,
            $this->hasFourOfAKind() => PokerHands::FourOfAKind,
            $this->hasFullHouse() => PokerHands::FullHouse,
            $this->hasFlush() => PokerHands::Flush,
            $this->hasStraight() => PokerHands::Straight,
            $this->hasThreeOfAKind() => PokerHands::ThreeOfAKind,
            $this->hasTwoPair() => PokerHands::TwoPair,
            $this->hasOnePair() => PokerHands::OnePair,
            default => PokerHands::HighCard,
        };
    }

    private function hasAce(): bool
    {
        return in_array(1, array_map(static fn (Card $card) => $card->value, $this->cards), true);
    }
}
