<?php
declare(strict_types=1);

namespace Rsaweb\Poker\Api\Controller;

use Rsaweb\Poker\Evaluate\PokerHandsEvaluate;
use Rsaweb\Poker\Exception\PokerHandsException;
use Rsaweb\Poker\Transformer\ShortStringToSuiteTransformer;
use Rsaweb\Poker\Validator\PokerHandValidator;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use function is_array;

final class EvaluatePokerHandsController
{
    /**
     * @param array{suites: string} $args
     * @return array{rank: string}
     */
    public function __invoke(array $args): array
    {
        if (!isset($args['suites']) || !is_array($args['suites'])) {
            throw new BadRequestException('Invalid Request: expected \'suites\' key with an array of suites');
        }

        try {
            PokerHandValidator::validate(...$args['suites']);
        } catch (PokerHandsException $e) {
            throw new BadRequestException($e->getMessage(), $e->getCode(), $e);
        }

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
