<?php
declare(strict_types=1);

namespace Rsaweb\Poker\Contracts;

interface Suite
{
    public function toString(): string;
    public function toShortString(): string;
}
