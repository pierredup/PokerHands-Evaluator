<?php
declare(strict_types=1);

namespace Rsaweb\Poker\Api\Http;

use FastRoute\RouteCollector;
use Rsaweb\Poker\Api\Controller\EvaluatePokerHandsController;

final class RouteLoader
{
    public static function load(RouteCollector $routeCollector): void
    {
        $routeCollector->addRoute(
            EvaluatePokerHandsController::ROUTE_PARAMS['method'],
            EvaluatePokerHandsController::ROUTE_PARAMS['path'],
            new EvaluatePokerHandsController()
        );
    }
}
