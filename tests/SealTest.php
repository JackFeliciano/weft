<?php

declare(strict_types=1);

use Weft\Home\Seal;
use Weft\Home\Shuttle;
use Weft\Tests\Assert;

return [
    ['a seal key carries eight characters or more', static function (): void {
        Assert::throws(static fn () => new Seal('short'), 'eight characters or more');
    }],
    ['the same fields seal the same way', static function (): void {
        $seal = new Seal('demo-key-only');
        Assert::same($seal->over('warp-a', 'warp-b', 1, 'hello'), $seal->over('warp-a', 'warp-b', 1, 'hello'));
    }],
    ['a changed body breaks the seal', static function (): void {
        $seal = new Seal('demo-key-only');
        $held = $seal->over('warp-a', 'warp-b', 1, 'hello');
        Assert::same(false, $seal->agrees($held, 'warp-a', 'warp-b', 1, 'hello there'));
    }],
    ['a changed sequence number breaks the seal', static function (): void {
        $seal = new Seal('demo-key-only');
        $held = $seal->over('warp-a', 'warp-b', 1, 'hello');
        Assert::same(false, $seal->agrees($held, 'warp-a', 'warp-b', 2, 'hello'));
    }],
    ['another key breaks the seal', static function (): void {
        $held = (new Seal('demo-key-only'))->over('warp-a', 'warp-b', 1, 'hello');
        Assert::same(false, (new Seal('another-key-here'))->agrees($held, 'warp-a', 'warp-b', 1, 'hello'));
    }],
    ['a shuffle of the fields breaks the seal', static function (): void {
        $seal = new Seal('demo-key-only');
        Assert::same(false, $seal->over('warp-a', 'warp-b', 1, 'hello') === $seal->over('warp-b', 'warp-a', 1, 'hello'));
    }],
    ['a shuttle names two warps and a body', static function (): void {
        Assert::throws(static fn () => new Shuttle('warp-a', 'warp-a', 1, 'loop', 'x'), 'cannot shuttle to itself');
        Assert::throws(static fn () => new Shuttle('warp-a', 'warp-b', 0, 'hello', 'x'), 'starts its thread at one');
        Assert::throws(static fn () => new Shuttle('warp-a', 'warp-b', 1, '  ', 'x'), 'carries a body');
    }],
];
