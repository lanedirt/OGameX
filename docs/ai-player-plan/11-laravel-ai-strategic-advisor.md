# Stage 11: Optional Laravel AI Strategic Advisor

The LLM remains optional and off by default.

Use the official Laravel AI SDK behind an application-level abstraction.

Conceptually:

```text
StrategicAdvisor

├── DeterministicStrategicAdvisor
└── LaravelAiStrategicAdvisor
```

The normal game AI must remain completely functional without an LLM.

## LLM responsibility

The LLM decides things such as:

```text
change strategic stance
favor economy
favor expansion
avoid particular threat
recover from fleet loss
temporarily reduce aggression
```

It does not decide routine actions.

It does not directly call:

```text
build
research
attack
database
```

## Structured output

The strategic advisor must return a bounded structured directive.

Example concepts:

```text
stance
goal priorities
weight modifiers
players to avoid
strategy expiration
confidence
```

All values are validated before being stored.

## Trigger frequency

LLM calls occur:

```text
when strategy expires
after major fleet loss
after repeated attacks
after major expansion event
after important strategic change
```

Not:

```text
every AI tick
every mine completion
every fleet return
```

## Cost controls

Server configuration should include:

```text
LLM enabled
provider/model
daily call limit
per-AI call limit
monthly/daily budget
```

When unavailable or budget-exhausted:

```text
use deterministic strategist
```

AI gameplay continues.

## Laravel AI capabilities to use

Use:

```text
agents
structured output
provider abstraction
provider failover
queueing
middleware
events/usage telemetry
test fakes
OpenAI-compatible local endpoints
```

Do not add unrelated AI SDK features to the gameplay path.

## Feature tests

Cover:

- LLM disabled by default
- normal AI never needs LLM
- routine tick does not call agent
- strategic trigger can call agent
- structured response is validated
- unknown goal rejected
- unknown player ID rejected
- provider failure falls back
- exhausted budget prevents call
- AI continues functioning with no credentials
- CI uses agent fakes only
- hidden game state is never included in strategic prompt
