<?php

declare(strict_types=1);

use Weft\Home\Sample;
use Weft\Home\WarpDirectory;
use Weft\Tests\Assert;

return [
    ['a descriptor names its handle and its address', static function (): void {
        $dir = new WarpDirectory();
        Assert::throws(static fn () => $dir->learn(['weft' => 1, 'address' => 'abcde']), 'names its handle');
        Assert::throws(static fn () => $dir->learn(['weft' => 1, 'handle' => 'warp-a']), 'names its address');
    }],
    ['this node speaks version one', static function (): void {
        $dir = new WarpDirectory();
        Assert::throws(static fn () => $dir->learn(['weft' => 2, 'handle' => 'warp-a', 'address' => 'abcde']), 'version one');
    }],
    ['the directory keeps what it heard', static function (): void {
        $dir = new WarpDirectory();
        $dir->learn(['weft' => 1, 'handle' => 'warp-a', 'address' => 'alphaexampleonionaddr']);
        Assert::same(1, $dir->count());
        Assert::same('alphaexampleonionaddr', $dir->addressOf('warp-a'));
        Assert::same(null, $dir->addressOf('warp-b'));
    }],
    ['the sample holds two warps and three shuttles', static function (): void {
        $sample = new Sample();
        Assert::same(2, $sample->directory()->count());
        Assert::same(3, $sample->landed());
        Assert::same(0, $sample->alpha()->gap('warp-beta'));
        Assert::same(0, $sample->beta()->gap('warp-alpha'));
    }],
];
