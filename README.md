# WEFT

## What the project is

WEFT is the InFolk network protocol. This repository holds a demonstration of the protocol, written in PHP 8.3. It runs without Composer or external packages.

A node runs WEFT and holds a warp. Each warp answers at a Tor hidden service and exposes 3 paths. The descriptor publishes the protocol version, handle, address and seat. The shuttle path accepts a sealed shuttle. The thread path returns landed shuttles in sequence order.

A shuttle names its sending warp, receiving warp, sequence number and body. Its seal covers those fields. The receiving warp recomputes the seal and keeps nothing if the seals differ. It also rejects a shuttle when it already holds that sequence number. When a thread has a gap, the response shows the first missing number.

The demo runs 2 warps and 3 landed shuttles on 1 server. It keeps the seal key in the code so readers can call the paths. The demonstration does not open a Tor circuit.

The repository also includes SPEC.md. This document states the protocol's words, 3 paths, addressing and 3 things the protocol leaves open.

## How to run it

Run the test suite from the repository root:

```bash
php tests/run.php
```


Start the demonstration with PHP's built-in server:

```bash
php -S 127.0.0.1:8105 -t showroom showroom/index.php
```


The application uses / as its default URL prefix. If a proxy passes a different prefix, set WEFT_BASE to that value.

The application uses this setting to account for the URL prefix passed by the proxy.

The demo runs locally on the PHP server. Although the protocol addresses warps through Tor hidden services, this demonstration does not open a Tor circuit.

## What the routes answer

The demonstration exposes 4 routes.

/ renders the 2 sample warps, their threads and the 5 steps a node takes.
/health answers with the number of known warps and landed shuttles.
/weft/1/descriptor answers with a warp descriptor.
/weft/1/thread/warp-beta answers with a thread and its gap.

The descriptor shows the information that a warp publishes about itself. The thread response shows landed shuttles in sequence order and identifies the first missing number when a gap exists.

## How the layout is arranged

The repository separates protocol logic from request handling, presentation, tests and deployment files.

src/Home/ holds Seal, Shuttle, Warp, WarpDirectory and Sample.
src/App/ holds the router, front controller and HTML.
showroom/ holds the entry file and stylesheet.
tests/ holds the test suite.
deploy/ holds the systemd unit and nginx location block.
SPEC.md describes the protocol.

The src/Home/ directory holds the main protocol structures and behaviour. The src/App/ directory handles requests and page output. The remaining files support the demonstration, testing and deployment.

## The state of the suite

The repository includes a hand-written test runner with 20 checks.

Run all checks with:

```bash
php tests/run.php
```


The runner requires no package installation or external test framework. It provides a direct way to check the demonstration from the command line.

## Licence

The repository uses the Mozilla Public License 2.0 (MPL 2.0). You may use, modify and distribute the code. You must publish changes to files covered by the licence.

