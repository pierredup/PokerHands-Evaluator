<?php
declare(strict_types=1);

namespace Rsaweb\Poker\Enum;

enum PokerHands
{
    case HighCard;
    case OnePair;
    case TwoPair;
    case ThreeOfAKind;
    case Straight;
    case Flush;
    case FullHouse;
    case FourOfAKind;
    case StraightFlush;
    case RoyalFlush;

    public function toString(): string
    {
        return match($this) {
            self::HighCard => 'High Card',
            self::OnePair => 'One Pair',
            self::TwoPair => 'Two Pair',
            self::ThreeOfAKind => 'Three of a Kind',
            self::Straight => 'Straight',
            self::Flush => 'Flush',
            self::FullHouse => 'Full House',
            self::FourOfAKind => 'Four of a Kind',
            self::StraightFlush => 'Straight Flush',
            self::RoyalFlush => 'Royal Flush',
        };
    }
}
