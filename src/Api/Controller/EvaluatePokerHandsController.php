<?php
declare(strict_types=1);

namespace Rsaweb\Poker\Api\Controller;

use JsonException;
use Rsaweb\Poker\Evaluate\PokerHandsEvaluate;
use Rsaweb\Poker\Exception\PokerHandsException;
use Rsaweb\Poker\Transformer\ShortStringToSuiteTransformer;
use Rsaweb\Poker\Validator\PokerHandValidator;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use function is_array;
use function json_decode;
use const JSON_THROW_ON_ERROR;

final class EvaluatePokerHandsController
{
    /**
     * @param array{suites: string} $args
     * @return array{rank: string}
     */
    public function __invoke(Request $request, array $args): array
    {
        try {
            $body = json_decode($request->getContent(), true, JSON_THROW_ON_ERROR, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new BadRequestException('Invalid JSON', $e->getCode(), $e);
        }

        if (!isset($body['suites']) || !is_array($body['suites'])) {
            throw new BadRequestException('Invalid Request: expected \'suites\' key with an array of suites');
        }

        try {
            PokerHandValidator::validate(...$body['suites']);
        } catch (PokerHandsException $e) {
            throw new BadRequestException($e->getMessage(), $e->getCode(), $e);
        }

        $transformer = new ShortStringToSuiteTransformer();

        $evaluator = new PokerHandsEvaluate(
            ...array_map(
                $transformer->transform(...),
                $body['suites']
            )
        );

        return [
            'rank' => $evaluator->getHighestRank()->toString(),
        ];
    }
}
