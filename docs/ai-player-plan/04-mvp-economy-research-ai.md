# Stage 4: MVP Economy and Research AI

This is the first actual playable AI.

Keep it in Laravel.

Do not introduce Rust yet.

The goal is to validate:

```text
AI state
    ↓
decision
    ↓
domain service
    ↓
game progresses
```

before optimizing anything.

## Decision model

Use utility scoring rather than long hardcoded build orders.

Each available action receives a score.

Economy can evaluate:

```text
energy shortage
metal production
crystal production
deuterium production
storage pressure
upgrade cost
upgrade time
production increase
resource balance
```

Research can evaluate:

```text
prerequisites
cost
unlock value
economy value
expansion value
military value
research duration
```

## Personality

Initial personalities can remain simple.

Examples:

```text
Miner
Balanced
Research-focused
```

Personality modifies utility weights.

Do not create entirely different decision implementations for every personality.

## Execution

The decision result should identify an existing candidate.

Example:

```text
Decision:
Upgrade Metal Mine on Planet 123
```

Laravel then executes it through the existing build queue service.

Same for research.

The AI engine must never directly:

```text
update building level
subtract resources
insert queue records
```

## Revalidation

State can change between decision and execution.

Therefore:

```text
decision
    ↓
existing OGameX service
    ↓
normal validation
```

If execution fails:

```text
record outcome
reschedule
```

Do not bypass the failure.

## MVP acceptance scenario

The main end-to-end test should be:

> Given a fresh AI-flagged account, repeated explicit AI ticks should allow the player to autonomously develop its economy and research using the same services and restrictions as a human player.

## Feature tests

Cover at minimum:

### Economy

- negative energy affects priority
- resource mine upgrades can be selected
- storage pressure affects priority
- unaffordable action is not executed
- queued building prevents illegal second build
- personality changes candidate ranking

### Research

- valid research can be selected
- missing prerequisite prevents research
- active research queue prevents second research
- insufficient resources prevent execution
- research-focused personality changes ranking

### Domain integration

- building action uses normal build service
- research action uses normal research service
- resources are deducted normally
- build/research times are normal
- AI receives no special speed or discount
- stale decision fails safely
- failed execution does not corrupt AI state
