<?php

use Rsaweb\Poker\Api\Controller\EvaluatePokerHandsController;
use Rsaweb\Poker\Exception\PokerHandsException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

require_once dirname(__DIR__).'/vendor/autoload.php';

$dispatcher = FastRoute\simpleDispatcher(static function(FastRoute\RouteCollector $routeCollector) {
    $routeCollector->addRoute('POST', '/api', new EvaluatePokerHandsController());
});

$request = Request::createFromGlobals();

$routeInfo = $dispatcher->dispatch($request->getMethod(), $request->getPathInfo());

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        (new JsonResponse(['error' => 'Not found'], 404))->send();
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        (new JsonResponse(['error' => 'Method not allowed'], 405))->send();
        break;
    case FastRoute\Dispatcher::FOUND:
        [, $handler, $vars] = $routeInfo;

        try {
            $body = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

            if (! isset($body['suites']) || ! is_array($body['suites'])) {
                (new JsonResponse(['error' => 'Invalid JSON: expected \'suites\' key with an array of suites'], 400))->send();
                break;
            }

            $vars = array_merge($vars, $body);
            (new JsonResponse($handler($vars), 200))->send();
        } catch (JsonException $e) {
            (new JsonResponse(['error' => 'Invalid JSON'], 400))->send();
        } catch (PokerHandsException $e) {
            (new JsonResponse(['error' => $e->getMessage()], 400))->send();
        } catch (Throwable $e) {
            (new JsonResponse(['error' => 'Internal server error: '. $e->getMessage()], 500))->send();
        }

        break;
}
