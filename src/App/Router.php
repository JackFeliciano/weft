<?php

declare(strict_types=1);

namespace Weft\App;

/**
 * Path in, handler out. The base prefix is stripped once, here, so a request
 * that arrives from nginx with its own prefix still matches the route table.
 */
final class Router
{
    /** @var array<string, callable> */
    private array $routes = [];

    public function __construct(private readonly string $base = '/')
    {
    }

    public function get(string $path, callable $handler): void
    {
        $this->routes[$this->normalise($path)] = $handler;
    }

    private function normalise(string $path): string
    {
        $path = '/' . trim($path, '/');

        return $path === '/' ? '/' : $path . '/';
    }

    private function strip(string $uri): string
    {
        $uri = parse_url($uri, PHP_URL_PATH) ?: '/';
        // The prefix is stripped once. A path that does not carry it, such as a
        // protocol path named in full, is left as it arrived.
        $base = rtrim($this->base, '/');
        if ($base !== '' && str_starts_with($uri, $base . '/')) {
            $uri = substr($uri, strlen($base));
        }

        return '/' . trim($uri, '/');
    }

    public function handle(string $method, string $uri): array
    {
        // A protocol path may carry the proxy prefix inside its own name, so the
        // table is asked twice: once with the prefix stripped, once as it came.
        $given = parse_url($uri, PHP_URL_PATH) ?: '/';
        foreach ([$this->strip($uri), '/' . trim($given, '/')] as $candidate) {
            $key = $this->normalise($candidate);
            if (isset($this->routes[$key])) {
                return (array) ($this->routes[$key])($candidate);
            }
        }

        return [404, ['error' => 'not found', 'path' => $this->strip($uri)]];
    }
}
