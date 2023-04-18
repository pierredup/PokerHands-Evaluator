<?php
declare(strict_types=1);

namespace Rsaweb\Poker\Enum;

use Rsaweb\Poker\Contracts\Suite;

enum Club: int implements Suite
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
}
