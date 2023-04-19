<?php
declare(strict_types=1);

namespace Rsaweb\Poker\Transformer;

use Rsaweb\Poker\Contracts\Suite;
use Rsaweb\Poker\Enum\Club;
use Rsaweb\Poker\Enum\Diamond;
use Rsaweb\Poker\Enum\Heart;
use Rsaweb\Poker\Enum\Spade;
use Rsaweb\Poker\Exception\InvalidCardException;
use function array_combine;
use function array_map;
use function array_merge;
use function in_array;

final class ShortStringToSuiteTransformer implements TransformerInterface
{
    /**
     * A map of all the suites converted to string with the short notation as the key
     * E.G. ['2H' => 'Two of Hearts']
     *
     * @var array<string, Suite>
     */
    private array $allSuites;

    /**
     * A list of all the suites in short notation
     *
     * @var list<string>
     */
    private array $suiteKeys;

    public function __construct()
    {
        $suites = array_merge(
            Spade::cases(),
            Diamond::cases(),
            Heart::cases(),
            Club::cases(),
        );

        $this->allSuites = array_combine(
            array_map(static fn(Suite $suite) => $suite->toShortString(), $suites),
            $suites
        );

        $this->suiteKeys = array_keys($this->allSuites);
    }

    /**
     * @throws InvalidCardException
     */
    public function transform(string $suite): Suite
    {
        if (!in_array($suite, $this->suiteKeys, true)) {
            throw new InvalidCardException([$suite]);
        }

        return $this->allSuites[$suite];
    }

    /**
     * @param list<string> $suites
     * @return list<Suite>
     *
     * @throws InvalidCardException
     */
    public function transformArray(array $suites): array
    {
        return array_map($this->transform(...), $suites);
    }
}
