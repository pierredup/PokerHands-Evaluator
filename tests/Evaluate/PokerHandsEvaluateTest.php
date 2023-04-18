<?php
declare(strict_types=1);

namespace Rsaweb\Poker\Tests\Evaluate;

use PHPUnit\Framework\Attributes\DataProvider;
use Rsaweb\Poker\Enum\Club;
use Rsaweb\Poker\Enum\Diamond;
use Rsaweb\Poker\Enum\Heart;
use Rsaweb\Poker\Enum\PokerHands;
use Rsaweb\Poker\Enum\Spade;
use Rsaweb\Poker\Evaluate\PokerHandsEvaluate;
use PHPUnit\Framework\TestCase;

/** @covers \Rsaweb\Poker\Evaluate\PokerHandsEvaluate */
final class PokerHandsEvaluateTest extends TestCase
{
    public function testHasRoyalFlush(): void
    {
        $evaluate = new PokerHandsEvaluate(
            Club::Ace,
            Club::King,
            Club::Queen,
            Club::Jack,
            Club::Ten,
        );

        self::assertTrue($evaluate->hasRoyalFlush());

        $evaluate = new PokerHandsEvaluate(
            Club::Ace,
            Club::Two,
            Club::Three,
            Club::Four,
            Club::Five,
        );

        self::assertFalse($evaluate->hasRoyalFlush());
    }

    public function testHasStraightFlush(): void
    {
        $evaluate = new PokerHandsEvaluate(
            Club::King,
            Club::Queen,
            Club::Jack,
            Club::Ten,
            Club::Nine,
        );

        self::assertTrue($evaluate->hasStraightFlush());

        $evaluate = new PokerHandsEvaluate(
            Club::King,
            Club::Queen,
            Club::Jack,
            Club::Ten,
            Heart::Nine,
        );

        self::assertFalse($evaluate->hasStraightFlush());
    }

    public function testHasFourOfAKind(): void
    {
        $evaluate = new PokerHandsEvaluate(
            Club::Five,
            Diamond::Five,
            Heart::Five,
            Spade::Five,
            Club::Ten,
            Diamond::Two,
        );

        self::assertTrue($evaluate->hasFourOfAKind());

        $evaluate = new PokerHandsEvaluate(
            Club::Five,
            Diamond::Five,
            Heart::Five,
            Spade::Four,
            Club::Ten,
            Diamond::Two,
        );

        self::assertFalse($evaluate->hasFourOfAKind());
    }

    public function testHasFullHouse(): void
    {
        $evaluate = new PokerHandsEvaluate(
            Spade::Six,
            Heart::Six,
            Diamond::Six,
            Club::King,
            Heart::King,
        );

        self::assertTrue($evaluate->hasFullHouse());

        $evaluate = new PokerHandsEvaluate(
            Spade::Six,
            Heart::Six,
            Diamond::Six,
            Club::King,
            Heart::Queen,
        );

        self::assertFalse($evaluate->hasFullHouse());
    }

    public function testHasFlush(): void
    {
        $evaluate = new PokerHandsEvaluate(
            Diamond::Jack,
            Diamond::Nine,
            Diamond::Eight,
            Diamond::Four,
            Diamond::Three,
        );

        self::assertTrue($evaluate->hasFlush());

        $evaluate = new PokerHandsEvaluate(
            Diamond::Jack,
            Diamond::Nine,
            Diamond::Eight,
            Diamond::Four,
            Spade::Three,
        );

        self::assertFalse($evaluate->hasFlush());
    }

    public function testHasStraight(): void
    {
        $evaluate = new PokerHandsEvaluate(
            Diamond::Ten,
            Spade::Nine,
            Heart::Eight,
            Diamond::Seven,
            Club::Six,
        );

        self::assertTrue($evaluate->hasStraight());

        $evaluate = new PokerHandsEvaluate(
            Diamond::Ten,
            Spade::Nine,
            Heart::Eight,
            Diamond::Seven,
            Club::Five,
        );

        self::assertFalse($evaluate->hasStraight());
    }

    public function testHasThreeOfAKind(): void
    {
        $evaluate = new PokerHandsEvaluate(
            Club::Queen,
            Spade::Queen,
            Heart::Queen,
            Heart::Nine,
            Spade::Two,
        );

        self::assertTrue($evaluate->hasThreeOfAKind());

        $evaluate = new PokerHandsEvaluate(
            Club::Queen,
            Spade::Queen,
            Heart::King,
            Heart::Nine,
            Spade::Two,
        );

        self::assertFalse($evaluate->hasThreeOfAKind());
    }

    public function testHasTwoPair(): void
    {
        $evaluate = new PokerHandsEvaluate(
            Heart::Jack,
            Spade::Jack,
            Club::Three,
            Spade::Three,
            Heart::Two,
        );

        self::assertTrue($evaluate->hasTwoPair());

        $evaluate = new PokerHandsEvaluate(
            Heart::Jack,
            Spade::Jack,
            Club::Three,
            Spade::Two,
            Heart::Four,
        );

        self::assertFalse($evaluate->hasTwoPair());
    }

    public function testHasOnePair(): void
    {
        $evaluate = new PokerHandsEvaluate(
            Spade::Ten,
            Heart::Ten,
            Spade::Eight,
            Heart::Seven,
            Club::Four,
        );

        self::assertTrue($evaluate->hasOnePair());

        $evaluate = new PokerHandsEvaluate(
            Spade::Ten,
            Heart::Nine,
            Spade::Eight,
            Heart::Seven,
            Club::Four,
        );

        self::assertFalse($evaluate->hasOnePair());
    }

    #[DataProvider('provideHighestRankData')]
    public function testGetHighestRank(array $suites, PokerHands $hand): void
    {
        $evaluate = new PokerHandsEvaluate(...$suites);

        self::assertEquals($hand, $evaluate->getHighestRank());
    }

    public static function provideHighestRankData(): iterable
    {
        yield 'Royal Flush' => [
            [
                Club::Ace,
                Club::King,
                Club::Queen,
                Club::Jack,
                Club::Ten,
            ],
            PokerHands::RoyalFlush,
        ];

        yield 'Straight Flush' => [
            [
                Club::King,
                Club::Queen,
                Club::Jack,
                Club::Ten,
                Club::Nine,
            ],
            PokerHands::StraightFlush,
        ];

        yield 'Four of a Kind' => [
            [
                Club::Five,
                Diamond::Five,
                Heart::Five,
                Spade::Five,
                Club::Ten,
            ],
            PokerHands::FourOfAKind,
        ];

        yield 'Full House' => [
            [
                Spade::Six,
                Heart::Six,
                Diamond::Six,
                Club::King,
                Heart::King,
            ],
            PokerHands::FullHouse,
        ];

        yield 'Flush' => [
            [
                Diamond::Jack,
                Diamond::Nine,
                Diamond::Eight,
                Diamond::Four,
                Diamond::Three,
            ],
            PokerHands::Flush,
        ];

        yield 'Straight' => [
            [
                Diamond::Ten,
                Spade::Nine,
                Heart::Eight,
                Diamond::Seven,
                Club::Six,
            ],
            PokerHands::Straight,
        ];

        yield 'Three of a Kind' => [
            [
                Club::Queen,
                Spade::Queen,
                Heart::Queen,
                Heart::Nine,
                Spade::Two,
            ],
            PokerHands::ThreeOfAKind,
        ];

        yield 'Two Pair' => [
            [
                Heart::Jack,
                Spade::Jack,
                Club::Three,
                Spade::Three,
                Heart::Two,
            ],
            PokerHands::TwoPair,
        ];

        yield 'One Pair' => [
            [
                Spade::Ten,
                Heart::Ten,
                Spade::Eight,
                Heart::Seven,
                Club::Four,
            ],
            PokerHands::OnePair,
        ];

        yield 'High Card' => [
            [
                Spade::Ten,
                Heart::Nine,
                Spade::Eight,
                Heart::Seven,
                Club::Four,
            ],
            PokerHands::HighCard,
        ];

        yield 'High Card Ace' => [
            [
                Spade::Ace,
                Heart::Nine,
                Spade::Eight,
                Heart::Seven,
                Club::Four,
            ],
            PokerHands::HighCard,
        ];
    }
}
