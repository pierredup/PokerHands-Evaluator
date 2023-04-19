<?php
declare(strict_types=1);

namespace Rsaweb\Poker\Api\Controller;

use Rsaweb\Poker\Evaluate\PokerHandsEvaluate;
use Rsaweb\Poker\Transformer\ShortStringToSuiteTransformer;
use Rsaweb\Poker\Validator\PokerHandValidator;

final class EvaluatePokerHandsController
{
    /**
     * @param array{suites: string} $args
     * @return array{rank: string}
     */
    public function __invoke(array $args): array
    {
        PokerHandValidator::validate(...$args['suites']);

        $transformer = new ShortStringToSuiteTransformer();

        $evaluator = new PokerHandsEvaluate(
            ...array_map(
                $transformer->transform(...),
                $args['suites']
            )
        );

        return [
            'rank' => $evaluator->getHighestRank()->toString(),
        ];
    }
}
