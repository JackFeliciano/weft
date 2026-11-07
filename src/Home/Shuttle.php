<?php

declare(strict_types=1);

namespace Weft\Home;

/**
 * One message on the wire. It names the warps at both ends, the sequence number in
 * its thread, the body and the seal over all of it.
 */
final class Shuttle
{
    public function __construct(
        public readonly string $from,
        public readonly string $to,
        public readonly int $seq,
        public readonly string $body,
        public readonly string $seal,
    ) {
        if ($from === $to) {
            throw new \RuntimeException('a warp cannot shuttle to itself');
        }
        if ($seq < 1) {
            throw new \RuntimeException('a shuttle starts its thread at one');
        }
        if (trim($body) === '') {
            throw new \RuntimeException('a shuttle carries a body');
        }
    }

    public function wire(): array
    {
        return ['from' => $this->from, 'to' => $this->to, 'seq' => $this->seq, 'body' => $this->body, 'seal' => $this->seal];
    }
}
