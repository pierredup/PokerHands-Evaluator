<?php
declare(strict_types=1);

namespace Rsaweb\Poker\Tests\Validator;

use Rsaweb\Poker\Exception\InvalidCardException;
use Rsaweb\Poker\Exception\InvalidNumberOfCards;
use Rsaweb\Poker\Exception\NonUniqueCardsException;
use Rsaweb\Poker\Validator\PokerHandValidator;
use PHPUnit\Framework\TestCase;

/** @covers \Rsaweb\Poker\Validator\PokerHandValidator */
final class PokerHandValidatorTest extends TestCase
{
    public function testValidateWithAllInvalidCards(): void
    {
        $this->expectException(InvalidCardException::class);
        $this->expectExceptionMessage('The following cards are invalid: A, B, C, D, E');

        PokerHandValidator::validate('A', 'B', 'C', 'D', 'E');
    }

    public function testValidateWithSomeInvalidCards(): void
    {
        $this->expectException(InvalidCardException::class);
        $this->expectExceptionMessage('The following cards are invalid: 1A');

        PokerHandValidator::validate('2H', '3D', 'JC', 'KH', '1A');
    }

    public function testValidateWithDuplicateCards(): void
    {
        $this->expectException(NonUniqueCardsException::class);
        $this->expectExceptionMessage('The following cards are duplicated: 2H');

        PokerHandValidator::validate('2H', '2H', 'JC', 'KH', '3D');
    }

    public function testValidateWithInvalidNumberOfCards(): void
    {
        $this->expectException(InvalidNumberOfCards::class);
        $this->expectExceptionMessage('Expected 5 cards, got 1');

        PokerHandValidator::validate('2H');
    }

    public function testValidateWithTooManyCards(): void
    {
        $this->expectException(InvalidNumberOfCards::class);
        $this->expectExceptionMessage('Expected 5 cards, got 6');

        PokerHandValidator::validate('2H', '3H', '4H', '5H', '6H', '7H');
    }
}
