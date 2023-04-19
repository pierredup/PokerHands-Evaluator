<?php

use Rsaweb\Poker\Api\Http\HttpHandler;
use Rsaweb\Poker\Api\Http\RouteLoader;
use Symfony\Component\HttpFoundation\Request;

require_once dirname(__DIR__).'/vendor/autoload.php';

$dispatcher = FastRoute\simpleDispatcher(RouteLoader::load(...));

$handler = new HttpHandler(Request::createFromGlobals());

$handler->handle($dispatcher)->send();
