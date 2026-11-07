<?php

declare(strict_types=1);

namespace Weft\Home;

/**
 * Two warps and the shuttles between them. The key stands in the demo so a reader
 * can watch a seal match. A deployment draws the key from the agreement the
 * protocol document leaves open.
 */
final class Sample
{
    private Warp $alpha;
    private Warp $beta;
    private WarpDirectory $directory;

    public function __construct()
    {
        $seal = new Seal('demo-key-only');
        $this->alpha = new Warp('warp-alpha', 'alphaexampleonionaddr', 12, $seal);
        $this->beta = new Warp('warp-beta', 'betaexampleonionaddr', 7, $seal);
        $this->directory = new WarpDirectory();
        $this->directory->learn($this->alpha->descriptor());
        $this->directory->learn($this->beta->descriptor());

        foreach ([
            ['warp-beta', 'warp-alpha', 1, 'hello from beta'],
            ['warp-beta', 'warp-alpha', 2, 'the second shuttle'],
            ['warp-alpha', 'warp-beta', 1, 'hello from alpha'],
        ] as [$from, $to, $seq, $body]) {
            $this->deliver(new Shuttle($from, $to, $seq, $body, $seal->over($from, $to, $seq, $body)));
        }
    }

    public function seal(): Seal
    {
        return new Seal('demo-key-only');
    }

    public function alpha(): Warp
    {
        return $this->alpha;
    }

    public function beta(): Warp
    {
        return $this->beta;
    }

    public function directory(): WarpDirectory
    {
        return $this->directory;
    }

    public function warpNamed(string $handle): Warp
    {
        if ($handle === $this->alpha->handle) {
            return $this->alpha;
        }
        if ($handle === $this->beta->handle) {
            return $this->beta;
        }

        throw new \RuntimeException('this node knows no warp called ' . $handle);
    }

    public function deliver(Shuttle $shuttle): void
    {
        $this->warpNamed($shuttle->to)->accept($shuttle);
    }

    public function landed(): int
    {
        return $this->alpha->landed() + $this->beta->landed();
    }
}
