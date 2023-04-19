<?php
declare(strict_types=1);

namespace Rsaweb\Poker\Api\Http;

use FastRoute\Dispatcher;
use JsonException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final readonly class HttpHandler
{
    public function __construct(private Request $request) {}

    public function handle(Dispatcher $dispatcher): Response
    {
        $routeInfo = $dispatcher->dispatch($this->request->getMethod(), $this->request->getPathInfo());

        return match ($routeInfo[0]) {
            Dispatcher::NOT_FOUND => $this->notFound(),
            Dispatcher::METHOD_NOT_ALLOWED => $this->methodNotAllowed(),
            Dispatcher::FOUND => $this->handleRoute($routeInfo),
        };
    }

    private function getResponse(array $data, int $statusCode): Response
    {
        return new JsonResponse($data, $statusCode);
    }

    private function notFound(): Response
    {
        return $this->getResponse(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
    }

    private function methodNotAllowed(): Response
    {
        return $this->getResponse(['error' => 'Method not allowed'], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @param array<int, int|string|callable|array> $routeInfo
     * @return Response
     */
    private function handleRoute(array $routeInfo): Response
    {
        [, $handler, $vars] = $routeInfo;

        try {
            return $this->getResponse($handler($this->request, $vars), Response::HTTP_OK);
        } catch (JsonException) {
            return $this->getResponse(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
        } catch (BadRequestException $e) {
            return $this->getResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (Throwable $e) {
            return $this->getResponse(['error' => 'Internal server error: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
