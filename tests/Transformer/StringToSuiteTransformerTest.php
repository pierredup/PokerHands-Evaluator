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
use Rsaweb\Poker\Transformer\StringToSuiteTransformer;
use PHPUnit\Framework\TestCase;

/** @covers \Rsaweb\Poker\Transformer\StringToSuiteTransformer */
final class StringToSuiteTransformerTest extends TestCase
{
    private StringToSuiteTransformer $transformer;

    protected function setUp(): void
    {
        $this->transformer = new StringToSuiteTransformer();
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