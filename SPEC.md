# WEFT

WEFT is the InFolk network protocol. A node that joins it holds a warp, and a warp
answers at a Tor hidden service. This document states the parts the demo builds and
the parts the protocol leaves open.

## Words

A **node** runs the protocol. A **warp** is one reachable address a node holds. A
**shuttle** is one message a node sends to a warp. A **thread** is the ordered run of
shuttles between two warps. A **seal** is the hash a warp writes over a shuttle it
accepted.

## What a warp answers

A warp answers three paths.

    GET /weft/1/descriptor
    POST /weft/1/shuttle
    GET /weft/1/thread/<id>

The descriptor names the protocol version, the warp handle, the seat the warp holds
and the paths it answers. A caller reads the descriptor before it sends a shuttle, so
a node can refuse a version it does not speak.

A shuttle carries the sending warp, the receiving warp, a sequence number, a body and
a seal over those fields. The receiving warp recomputes the seal. A seal that does not
match ends the shuttle there, and the warp keeps no record of it.

A thread answers with the shuttles it holds in sequence order. A warp refuses a
sequence number it already holds, so a replay of an old shuttle lands nowhere.

## Address

A warp reachable over Tor carries an onion address of 56 characters and a port. The
protocol makes no claim about how a node publishes the address. A node that publishes
nothing stays reachable by the warps it told already.

## What this repository holds

The demo is PHP 8.3 with no Composer and no packages. It holds a warp, a seal, a
shuttle, a thread and a directory of warps, and it answers the three paths through a
small local server so a reader can watch a shuttle land. Nothing here opens a Tor
circuit. The seal is the arithmetic the protocol needs, and the demo proves the
arithmetic.

## Open

The protocol leaves three things to a later document: how two warps agree a shared
key for the seal, how a node hands its address to a new peer, and what a warp does
with a shuttle whose sequence gap it cannot close.
