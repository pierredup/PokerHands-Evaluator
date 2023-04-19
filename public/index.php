<?php

use Rsaweb\Poker\Api\Controller\EvaluatePokerHandsController;
use Rsaweb\Poker\Api\Http\HttpHandler;
use Symfony\Component\HttpFoundation\Request;

require_once dirname(__DIR__).'/vendor/autoload.php';

$dispatcher = FastRoute\simpleDispatcher(static function(FastRoute\RouteCollector $routeCollector) {
    $routeCollector->addRoute('POST', '/api', new EvaluatePokerHandsController());
});

$handler = new HttpHandler(Request::createFromGlobals());

$handler->handle($dispatcher)->send();
