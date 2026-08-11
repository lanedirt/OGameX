# Stage 6: Expansion and Colonization

Add autonomous empire expansion.

AI evaluates:

```text
planet count
Astrophysics
colony slots
colony ship availability
planet location
resource requirements
```

Initially this can still use utility goals rather than a general-purpose planner.

A multi-step expansion state can be persisted:

```text
goal = colonize
```

Routine economic/research utility can then favor prerequisites required by that goal.

## Feature tests

Cover:

- AI recognizes available colony slot
- insufficient Astrophysics prevents colonization
- missing Colony Ship triggers prerequisite behavior
- occupied position cannot be selected
- colonization uses normal FleetMissionService
- colony obeys normal game restrictions
