<?php
declare(strict_types=1);

namespace Rsaweb\Poker\Contracts;

use BackedEnum;

interface Suite extends BackedEnum
{
    public function toString(): string;
    public function toShortString(): string;
}
