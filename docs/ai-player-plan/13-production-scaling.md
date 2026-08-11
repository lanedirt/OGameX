# Stage 13: Production Scaling

The architecture should work first with OGameX's existing queue setup.

Redis should not be required for the MVP.

For larger servers, Redis can be supported as a scaling profile.

Laravel Horizon can then be an optional operational dependency for Redis-based deployments.

## Queues

Suggested priorities:

```text
ai-reflex
normal gameplay jobs
ai-think
ai-strategy
ai-llm
```

LLM work should never delay game progression or fleet processing.

## Metrics

Track:

```text
AI count
active AI count
due AI count
think jobs/minute
queue lag
queries/decision
Laravel decision time
Rust decision time
candidate count
planner nodes
LLM calls
LLM cost/usage
fallback rate
action failures
```

## Load scenarios

Test:

```text
1 AI
100 AI
1,000 AI
1,000 mostly sleeping AI
1,000 simultaneously due AI
LLM unavailable
Rust unavailable
database queue
Redis queue
```

The desired scaling property remains:

> CPU usage should scale primarily with AI activity, not the total number of AI accounts.
