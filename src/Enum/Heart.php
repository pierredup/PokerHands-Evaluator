<?php
declare(strict_types=1);

namespace Rsaweb\Poker\Enum;

use Rsaweb\Poker\Contracts\Card;

enum Heart: int implements Card
{
    case Ace = 1;
    case Two = 2;
    case Three = 3;
    case Four = 4;
    case Five = 5;
    case Six = 6;
    case Seven = 7;
    case Eight = 8;
    case Nine = 9;
    case Ten = 10;
    case Jack = 11;
    case Queen = 12;
    case King = 13;

    public function toString(): string
    {
        if ($this->value > 1 && $this->value < 11) {
            return $this->value . ' of Hearts';
        }

        return $this->name . ' of Hearts';
    }

    public function toShortString(): string
    {
        if ($this->value > 1 && $this->value < 11) {
            return $this->value . 'H';
        }

        return $this->name[0] . 'H';
    }
}
