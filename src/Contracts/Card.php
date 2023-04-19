<?php
declare(strict_types=1);

namespace Rsaweb\Poker\Contracts;

use BackedEnum;

interface Card extends BackedEnum
{
    public function toString(): string;
    public function toShortString(): string;
}
