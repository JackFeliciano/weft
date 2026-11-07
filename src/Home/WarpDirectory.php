<?php

declare(strict_types=1);

namespace Weft\Home;

/**
 * The warps one node knows about. A node reads a descriptor before it sends to a
 * warp, so the directory keeps what it heard.
 */
final class WarpDirectory
{
    /** @var array<string, array<string, mixed>> */
    private array $known = [];

    public function learn(array $descriptor): void
    {
        if (($descriptor['weft'] ?? null) !== 1) {
            throw new \RuntimeException('this node speaks weft version one');
        }
        foreach (['handle', 'address'] as $field) {
            if (($descriptor[$field] ?? '') === '') {
                throw new \RuntimeException('a descriptor names its ' . $field);
            }
        }
        $this->known[(string) $descriptor['handle']] = $descriptor;
    }

    /** @return array<string, array<string, mixed>> */
    public function known(): array
    {
        return $this->known;
    }

    public function count(): int
    {
        return count($this->known);
    }

    public function addressOf(string $handle): ?string
    {
        return $this->known[$handle]['address'] ?? null;
    }
}
