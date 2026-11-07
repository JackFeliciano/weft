<?php

declare(strict_types=1);

namespace Weft\App;

use Weft\Home\Sample;

/**
 * The front controller. It answers the demo page, the health answer and the three
 * protocol paths, so a reader can call a warp the way a peer would.
 */
final class Front
{
    public function __construct(private readonly string $base = '/')
    {
    }

    public function run(): void
    {
        // The router strips the prefix before it looks a path up, so the
        // route table holds the bare paths and the links keep the prefix.
        $router = new Router($this->base);
        $views = new Views($this->base);

        $router->get('/', static fn (): array => [200, [
            'html' => $views->page(new Sample()),
        ]]);
        $router->get('/health', static fn (): array => [200, ['json' => [
            'ok' => true,
            'weft' => 1,
            'warps' => (new Sample())->directory()->count(),
            'landed' => (new Sample())->landed(),
        ]]]);
        // The protocol paths sit under /weft/1/ on the site, and the prefix the
        // proxy adds is already inside that name.
        $router->get('/weft/1/descriptor', static fn (): array => [200, [
            'json' => (new Sample())->alpha()->descriptor(),
        ]]);
        $router->get('/weft/1/thread/warp-beta', static fn (): array => [200, ['json' => [
            'warp' => 'warp-alpha',
            'from' => 'warp-beta',
            'shuttles' => array_map(
                static fn ($s): array => $s->wire(),
                (new Sample())->alpha()->thread('warp-beta'),
            ),
            'gap' => (new Sample())->alpha()->gap('warp-beta'),
        ]]]);

        [$status, $body] = $router->handle(
            $_SERVER['REQUEST_METHOD'] ?? 'GET',
            $_SERVER['REQUEST_URI'] ?? '/',
        );

        http_response_code($status);
        if (isset($body['json'])) {
            header('Content-Type: application/json');
            echo json_encode($body['json'], JSON_UNESCAPED_SLASHES), "\n";

            return;
        }
        header('Content-Type: text/html; charset=utf-8');
        if (isset($body['html'])) {
            echo $body['html'];

            return;
        }
        echo $views->missing();
    }
}
