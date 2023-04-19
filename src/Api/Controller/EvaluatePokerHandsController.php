<?php
declare(strict_types=1);

namespace Rsaweb\Poker\Api\Controller;

use JsonException;
use Rsaweb\Poker\Evaluate\PokerHandsEvaluate;
use Rsaweb\Poker\Exception\PokerHandsException;
use Rsaweb\Poker\Transformer\ShortStringToCardTransformer;
use Rsaweb\Poker\Validator\PokerHandValidator;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use function is_array;
use function json_decode;
use const JSON_THROW_ON_ERROR;

final class EvaluatePokerHandsController
{
    public const ROUTE_PARAMS = [
        'method' => 'POST',
        'path' => '/api',
    ];

    /**
     * @param array{cards: string} $args
     * @return array{rank: string}
     */
    public function __invoke(Request $request, array $args): array
    {
        try {
            $body = json_decode($request->getContent(), true, JSON_THROW_ON_ERROR, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new BadRequestException('Invalid JSON', $e->getCode(), $e);
        }

        if (!isset($body['cards']) || !is_array($body['cards'])) {
            throw new BadRequestException('Invalid Request: expected \'cards\' key with an array of cards');
        }

        try {
            PokerHandValidator::validate(...$body['cards']);
        } catch (PokerHandsException $e) {
            throw new BadRequestException($e->getMessage(), $e->getCode(), $e);
        }

        $transformer = new ShortStringToCardTransformer();

        $evaluator = new PokerHandsEvaluate(
            ...$transformer->transformArray($body['cards'])
        );

        return [
            'rank' => $evaluator->getHighestRank()->toString(),
        ];
    }
}
