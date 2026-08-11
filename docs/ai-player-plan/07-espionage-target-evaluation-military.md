# Stage 7: Espionage, Target Evaluation and Military Behavior

Now add interaction with other players.

## Perception boundary expands

Enemy information must come only from legal sources:

```text
public galaxy data
espionage reports
combat reports
phalanx where available
historical memory
```

Never send current database truth about enemies directly to the AI engine.

## Target scoring

Evaluate:

```text
expected loot
known fleet
known defense
confidence
distance
fuel cost
travel time
historical profitability
retaliation risk
```

## Defensive reflexes

Some decisions should bypass normal strategic thinking.

Examples:

```text
dangerous incoming attack
fleet returned
critical resource exposure
```

These should use a higher-priority AI queue/event path.

No LLM is involved.

## Feature tests

Cover:

- unknown enemy state is not visible
- espionage creates usable knowledge
- stale espionage reduces confidence
- profitable target outranks poor target
- dangerous target can be rejected
- target evaluation respects distance/fuel
- attack uses FleetMissionService
- espionage uses FleetMissionService
- incoming attack can trigger defensive behavior
- AI cannot fleetsave using hidden information
