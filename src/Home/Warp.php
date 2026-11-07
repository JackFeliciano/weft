<?php

declare(strict_types=1);

namespace Weft\Home;

/**
 * A warp holds the threads it accepted and answers three paths. It checks the seal
 * on the way in, so a shuttle that does not seal lands nowhere.
 */
final class Warp
{
    /** @var array<string, list<Shuttle>> */
    private array $threads = [];

    public function __construct(
        public readonly string $handle,
        public readonly string $address,
        public readonly int $seat,
        private readonly Seal $seal,
    ) {
        if (trim($handle) === '') {
            throw new \RuntimeException('a warp carries a handle');
        }
        if (strlen($address) < 5) {
            throw new \RuntimeException('a warp carries an address of five characters or more');
        }
        if ($seat < 1) {
            throw new \RuntimeException('a warp holds a seat of one or more');
        }
    }

    public function descriptor(): array
    {
        return [
            'weft' => 1,
            'handle' => $this->handle,
            'address' => $this->address,
            'seat' => $this->seat,
            'paths' => ['/weft/1/descriptor', '/weft/1/shuttle', '/weft/1/thread/<id>'],
        ];
    }

    public function accept(Shuttle $shuttle): void
    {
        if ($shuttle->to !== $this->handle) {
            throw new \RuntimeException('the shuttle names another warp');
        }
        if (!$this->seal->agrees($shuttle->seal, $shuttle->from, $shuttle->to, $shuttle->seq, $shuttle->body)) {
            throw new \RuntimeException('the seal does not agree, so the warp keeps nothing');
        }
        $thread = $this->threads[$shuttle->from] ?? [];
        foreach ($thread as $held) {
            if ($held->seq === $shuttle->seq) {
                throw new \RuntimeException('the warp already holds that sequence number');
            }
        }
        $thread[] = $shuttle;
        usort($thread, static fn (Shuttle $a, Shuttle $b): int => $a->seq <=> $b->seq);
        $this->threads[$shuttle->from] = $thread;
    }

    /** @return list<Shuttle> */
    public function thread(string $from): array
    {
        return $this->threads[$from] ?? [];
    }

    public function threads(): int
    {
        return count($this->threads);
    }

    public function landed(): int
    {
        $sum = 0;
        foreach ($this->threads as $thread) {
            $sum += count($thread);
        }

        return $sum;
    }

    /** The first missing sequence number, or zero when the run is whole. */
    public function gap(string $from): int
    {
        $want = 1;
        foreach ($this->thread($from) as $held) {
            if ($held->seq !== $want) {
                return $want;
            }
            $want++;
        }

        return 0;
    }
}
