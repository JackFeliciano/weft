<?php

declare(strict_types=1);

use Weft\Home\Seal;
use Weft\Home\Shuttle;
use Weft\Home\Warp;
use Weft\Tests\Assert;

function wft_warp(): Warp
{
    return new Warp('warp-alpha', 'alphaexampleonionaddr', 12, new Seal('demo-key-only'));
}

function wft_shuttle(int $seq, string $body = 'hello from beta'): Shuttle
{
    $seal = new Seal('demo-key-only');

    return new Shuttle('warp-beta', 'warp-alpha', $seq, $body, $seal->over('warp-beta', 'warp-alpha', $seq, $body));
}

return [
    ['a warp carries a handle, an address of five characters and a seat', static function (): void {
        Assert::throws(static fn () => new Warp(' ', 'alphaexampleonionaddr', 12, new Seal('demo-key-only')), 'carries a handle');
        Assert::throws(static fn () => new Warp('warp-a', 'abc', 12, new Seal('demo-key-only')), 'five characters');
        Assert::throws(static fn () => new Warp('warp-a', 'alphaexampleonionaddr', 0, new Seal('demo-key-only')), 'seat of one or more');
    }],
    ['the descriptor names the version and the three paths', static function (): void {
        $d = wft_warp()->descriptor();
        Assert::same(1, $d['weft']);
        Assert::same('warp-alpha', $d['handle']);
        Assert::same(3, count($d['paths']));
    }],
    ['a sealed shuttle lands in the thread', static function (): void {
        $warp = wft_warp();
        $warp->accept(wft_shuttle(1));
        Assert::same(1, $warp->landed());
        Assert::same(1, $warp->threads());
        Assert::same('hello from beta', $warp->thread('warp-beta')[0]->body);
    }],
    ['a shuttle with a broken seal lands nowhere', static function (): void {
        $warp = wft_warp();
        $seal = new Seal('demo-key-only');
        $wrong = new Shuttle('warp-beta', 'warp-alpha', 1, 'hello from beta', $seal->over('warp-beta', 'warp-alpha', 1, 'something else'));
        Assert::throws(static fn () => $warp->accept($wrong), 'seal does not agree');
        Assert::same(0, $warp->landed());
        Assert::same(0, $warp->threads());
    }],
    ['a warp refuses a shuttle addressed elsewhere', static function (): void {
        $warp = wft_warp();
        $seal = new Seal('demo-key-only');
        $elsewhere = new Shuttle('warp-beta', 'warp-gamma', 1, 'hello', $seal->over('warp-beta', 'warp-gamma', 1, 'hello'));
        Assert::throws(static fn () => $warp->accept($elsewhere), 'names another warp');
    }],
    ['a repeated sequence number lands nowhere', static function (): void {
        $warp = wft_warp();
        $warp->accept(wft_shuttle(1));
        Assert::throws(static fn () => $warp->accept(wft_shuttle(1)), 'already holds that sequence number');
        Assert::same(1, $warp->landed());
    }],
    ['a thread answers in sequence order', static function (): void {
        $warp = wft_warp();
        $warp->accept(wft_shuttle(2, 'second'));
        $warp->accept(wft_shuttle(1, 'first'));
        $thread = $warp->thread('warp-beta');
        Assert::same(1, $thread[0]->seq);
        Assert::same(2, $thread[1]->seq);
    }],
    ['a hole in the run shows as a gap', static function (): void {
        $warp = wft_warp();
        $warp->accept(wft_shuttle(1));
        Assert::same(0, $warp->gap('warp-beta'));
        $warp->accept(wft_shuttle(3));
        Assert::same(2, $warp->gap('warp-beta'));
    }],
    ['an unknown warp answers an empty thread', static function (): void {
        Assert::same([], wft_warp()->thread('warp-nobody'));
    }],
];
