<?php

declare(strict_types=1);

namespace Weft\Home;

/**
 * The seal over a shuttle. A warp writes it over the fields it accepted, and the
 * receiving warp recomputes it. A seal that does not match ends the shuttle.
 *
 * The demo uses a keyed hash over a field list. A real deployment swaps the key
 * agreement for the one the protocol document leaves open, and the fields stay.
 */
final class Seal
{
    public function __construct(private readonly string $key)
    {
        if (strlen($key) < 8) {
            throw new \RuntimeException('a seal key carries eight characters or more');
        }
    }

    public function over(string $from, string $to, int $seq, string $body): string
    {
        // The field count goes first, so two different splits cannot seal the same.
        $wire = implode("\n", ['4', $from, $to, (string) $seq, $body]);

        return hash_hmac('sha256', $wire, $this->key);
    }

    public function agrees(string $seal, string $from, string $to, int $seq, string $body): bool
    {
        return hash_equals($this->over($from, $to, $seq, $body), $seal);
    }
}
