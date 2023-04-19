<?php
declare(strict_types=1);

namespace Rsaweb\Poker\Tests\Transformer;

use PHPUnit\Framework\Attributes\DataProvider;
use Rsaweb\Poker\Contracts\Card;
use Rsaweb\Poker\Enum\Club;
use Rsaweb\Poker\Enum\Diamond;
use Rsaweb\Poker\Enum\Heart;
use Rsaweb\Poker\Enum\Spade;
use Rsaweb\Poker\Exception\InvalidCardException;
use Rsaweb\Poker\Transformer\ShortStringToCardTransformer;
use PHPUnit\Framework\TestCase;

/** @covers \Rsaweb\Poker\Transformer\ShortStringToCardTransformer */
final class StringToCardTransformerTest extends TestCase
{
    private ShortStringToCardTransformer $transformer;

    protected function setUp(): void
    {
        $this->transformer = new ShortStringToCardTransformer();
    }

    #[DataProvider('provideValidCard')]
    public function testTransformWithValidCards(string $key, Card $card): void
    {
        self::assertSame($card, $this->transformer->transform($key));
    }

    #[DataProvider('provideInvalidCard')]
    public function testTransformWithInvalidCards(string $key): void
    {
        $this->expectException(InvalidCardException::class);
        $this->expectExceptionMessage('The following cards are invalid: ' . $key);

        $this->transformer->transform($key);
    }

    public function testTransformArrayWithValidCards(): void
    {
        $shortStringCards = ['2H', '3D', '10C', 'AS', 'KC'];
        $cards = [
            Heart::Two,
            Diamond::Three,
            Club::Ten,
            Spade::Ace,
            Club::King,
        ];

        self::assertSame($cards, $this->transformer->transformArray($shortStringCards));
    }

    public function testTransformArrayWithInvalidCards(): void
    {
        $cards = ['2H', '3D', '10C', 'AS', 'KA'];
        $this->expectException(InvalidCardException::class);
        $this->expectExceptionMessage('The following cards are invalid: KA');

        $this->transformer->transformArray($cards);
    }

    public static function provideValidCard(): iterable
    {
        yield ['2H', Heart::Two];
        yield ['3D', Diamond::Three];
        yield ['10C', Club::Ten];
        yield ['AS', Spade::Ace];
        yield ['KC', Club::King];
    }

    public static function provideInvalidCard(): iterable
    {
        yield ['2'];
        yield ['D'];
        yield ['10P'];
        yield ['1S'];
        yield ['KA'];
    }
}
