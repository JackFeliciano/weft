# WEFT roadmap

WEFT answers three paths today and the demonstration proves the seal, the repeat
refusal and the order of a thread. The items below are open, and each one names
what a reader can check when it lands.

## Version 1 is not whole

- The protocol leaves three things open: the key agreement, the address form and
  the size a body may reach. SPEC.md states each one as open, and a version 1 that
  peers can trust needs a ruling on all three.
- A shuttle carries no expiry, so a replayed shuttle inside its sequence window
  still lands. Add a day stamp and a window the receiver enforces.
- The gap answer names the first missing number only. A thread that lost two runs
  reports one, so a receiver cannot tell a lost tail from a hole.

## The demo is not a node

- 20 checks cover the model. Nothing covers a real Tor hidden service, and no
  check opens a circuit.
- Two warps run in one process with one key in the code. A real node holds one
  warp, draws its key from an agreement, and never ships a key in source.
- No path limits how many shuttles a warp keeps, so a thread can grow until the
  process runs out of memory. A cap and a drop rule are owed.

## What a second peer needs

- A descriptor cache so a node does not ask for the same warp twice in one run.
- A refusal answer with a reason word, so a sender learns why a shuttle landed
  nowhere.
- A thread answer that names the last number the receiver holds, so a sender knows
  where to resume.

## Checks owed

- A shuttle with a body of the largest allowed size.
- A thread with two holes.
- A warp that receives shuttles from three senders and keeps the threads apart.
