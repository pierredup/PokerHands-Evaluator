<?php
declare(strict_types=1);

namespace Rsaweb\Poker\Tests\Transformer;

use PHPUnit\Framework\Attributes\DataProvider;
use Rsaweb\Poker\Contracts\Suite;
use Rsaweb\Poker\Enum\Club;
use Rsaweb\Poker\Enum\Diamond;
use Rsaweb\Poker\Enum\Heart;
use Rsaweb\Poker\Enum\Spade;
use Rsaweb\Poker\Exception\InvalidCardException;
use Rsaweb\Poker\Transformer\ShortStringToSuiteTransformer;
use PHPUnit\Framework\TestCase;

/** @covers \Rsaweb\Poker\Transformer\ShortStringToSuiteTransformer */
final class StringToSuiteTransformerTest extends TestCase
{
    private ShortStringToSuiteTransformer $transformer;

    protected function setUp(): void
    {
        $this->transformer = new ShortStringToSuiteTransformer();
    }

    #[DataProvider('provideValidSuite')]
    public function testTransformWithValidSuites(string $key, Suite $suite): void
    {
        self::assertSame($suite, $this->transformer->transform($key));
    }

    #[DataProvider('provideInvalidSuite')]
    public function testTransformWithInvalidSuites(string $key): void
    {
        $this->expectException(InvalidCardException::class);
        $this->expectExceptionMessage('The following cards are invalid: ' . $key);

        $this->transformer->transform($key);
    }

    public function testTransformArrayWithValidCards(): void
    {
        $cards = ['2H', '3D', '10C', 'AS', 'KC'];
        $suites = [
            Heart::Two,
            Diamond::Three,
            Club::Ten,
            Spade::Ace,
            Club::King,
        ];

        self::assertSame($suites, $this->transformer->transformArray($cards));
    }

    public function testTransformArrayWithInvalidCards(): void
    {
        $cards = ['2H', '3D', '10C', 'AS', 'KA'];
        $this->expectException(InvalidCardException::class);
        $this->expectExceptionMessage('The following cards are invalid: KA');

        $this->transformer->transformArray($cards);
    }

    public static function provideValidSuite(): iterable
    {
        yield ['2H', Heart::Two];
        yield ['3D', Diamond::Three];
        yield ['10C', Club::Ten];
        yield ['AS', Spade::Ace];
        yield ['KC', Club::King];
    }

    public static function provideInvalidSuite(): iterable
    {
        yield ['2'];
        yield ['D'];
        yield ['10P'];
        yield ['1S'];
        yield ['KA'];
    }
}
