<?php

declare(strict_types=1);

namespace Weft\App;

use Weft\Home\Sample;
use Weft\Home\Warp;

/**
 * The HTML. Every string that reaches the page goes through h(), so a body with an
 * ampersand in it cannot break the markup.
 */
final class Views
{
    public function __construct(private readonly string $base = '/')
    {
    }

    private function h(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    public function header(string $title): string
    {
        $base = $this->h($this->base);
        $t = $this->h($title);

        return <<<HTML
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{$t}</title>
<base href="{$base}">
<link rel="stylesheet" href="assets/site.css">
</head>
<body>
<main>
HTML;
    }

    public function footer(): string
    {
        return "</main>\n</body>\n</html>\n";
    }

    public function page(Sample $sample): string
    {
        $TITLE = 'WEFT, a network of nodes reached in private';
        $LEDE = 'WEFT is a network protocol built around independent nodes that communicate through Tor hidden services, where each node operates as a warp and carries its own strand of the network.';
        $WHAT = 'A node publishes a descriptor at its hidden service, and participants use InDX identities to establish trust. Signed events form a chain that records no account balances. Sealed messages called shuttles carry the communication, with gossip, catch up and handshake rules governing their exchange.';

        $out = $this->header($TITLE);
        $out .= '<h1>' . $this->h($TITLE) . '</h1>' . "\n";
        $out .= '<p class="lede">' . $this->h($LEDE) . '</p>' . "\n";
        $out .= '<p>' . $this->h($WHAT) . '</p>' . "\n";
        $out .= '<p class="stage">Protocol version 1. Warps known: ' . $sample->directory()->count()
            . '. Shuttles landed: ' . $sample->landed() . '. Descriptor at <code>weft/1/descriptor</code>. Thread at <code>weft/1/thread/warp-beta</code>.</p>' . "\n";

        $out .= '<div class="warps">' . "\n";
        foreach ([
            ['Start a warp', 'An independent node starts as a warp, publishes its descriptor and joins the strand of the network it answers.'],
            ['Sign the event', 'A participant signs an event with an InDX identity, and the chain records it with no balance.'],
            ['Exchange shuttles', 'The warp hands a sealed shuttle to another warp, and the gossip, catch up and handshake rules govern the exchange.'],
        ] as [$step, $line]) {
            $out .= '<li><strong>' . $this->h($step) . '</strong> ' . $this->h($line) . '</li>' . "\n";
        }
        $out .= '</ol>' . "\n";
        $out .= $this->footer();

        return $out;
    }

    private function card(Sample $sample, Warp $warp): string
    {
        $out = '<article class="warp">' . "\n";
        $out .= '<h2>' . $this->h($warp->handle) . '</h2>' . "\n";
        $out .= '<p class="where">Address <code>' . $this->h($warp->address) . '</code>, seat ' . $warp->seat . '.</p>' . "\n";
        $out .= '<p class="where">Threads: ' . $warp->threads() . '. Shuttles landed: ' . $warp->landed() . '.</p>' . "\n";
        $rows = [];
        foreach ($warp->threads() === 0 ? [] : $this->peers($sample, $warp) as $peer) {
            foreach ($warp->thread($peer) as $shuttle) {
                $rows[] = $shuttle;
            }
        }
        if ($rows === []) {
            $out .= '<p class="where">No thread sits on this warp.</p>' . "\n";
        } else {
            $out .= '<table class="thread"><thead><tr><th>From</th><th>Seq</th><th>Body</th><th>Seal</th></tr></thead><tbody>' . "\n";
            foreach ($rows as $shuttle) {
                $out .= '<tr><td>' . $this->h($shuttle->from) . '</td><td class="num">' . $shuttle->seq
                    . '</td><td>' . $this->h($shuttle->body) . '</td><td class="seal">' . $this->h(substr($shuttle->seal, 0, 24)) . '</td></tr>' . "\n";
            }
            $out .= '</tbody></table>' . "\n";
        }
        $out .= '</article>' . "\n";

        return $out;
    }

    /** @return list<string> */
    private function peers(Sample $sample, Warp $warp): array
    {
        $out = [];
        foreach ($sample->directory()->known() as $handle => $descriptor) {
            if ($handle !== $warp->handle) {
                $out[] = (string) $handle;
            }
        }

        return $out;
    }

    public function missing(): string
    {
        return $this->header('Not found') . "<h1>Not found</h1>\n<p>That path does not exist here.</p>\n" . $this->footer();
    }
}
