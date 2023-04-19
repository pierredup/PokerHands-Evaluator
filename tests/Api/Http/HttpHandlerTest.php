<?php
declare(strict_types=1);

namespace Rsaweb\Poker\Tests\Api\Http;

use FastRoute\Dispatcher;
use Rsaweb\Poker\Api\Http\HttpHandler;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

/** @covers \Rsaweb\Poker\Api\Http\HttpHandler */
final class HttpHandlerTest extends TestCase
{
    public function testHandleWithRouteNotFound(): void
    {
        $handler = new HttpHandler(new Request());

        $dispatcher = $this->createMock(Dispatcher::class);

        $dispatcher
            ->expects(self::once())
            ->method('dispatch')
            ->with('GET', '/')
            ->willReturn([Dispatcher::NOT_FOUND]);

        $response = $handler->handle($dispatcher);

        self::assertSame(404, $response->getStatusCode());
    }

    public function testHandleWithMethodNotAllowed(): void
    {
        $handler = new HttpHandler(new Request());

        $dispatcher = $this->createMock(Dispatcher::class);

        $dispatcher
            ->expects(self::once())
            ->method('dispatch')
            ->with('GET', '/')
            ->willReturn([Dispatcher::METHOD_NOT_ALLOWED]);

        $response = $handler->handle($dispatcher);

        self::assertSame(400, $response->getStatusCode());
    }

    public function testHandleWithRouteFound(): void
    {
        $handler = new HttpHandler(new Request(content: '[]'));

        $dispatcher = $this->createMock(Dispatcher::class);

        $dispatcher
            ->expects(self::once())
            ->method('dispatch')
            ->with('GET', '/')
            ->willReturn([Dispatcher::FOUND, static fn () => [], []]);

        $response = $handler->handle($dispatcher);

        self::assertSame(200, $response->getStatusCode());
    }
}
