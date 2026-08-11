# Stage 2: Scheduler, Explicit Tick and Queue

This is the core infrastructure required before any AI intelligence.

## Scheduler

Run a lightweight scheduler that finds due AI accounts.

Do not create one permanent process per player.

Query only:

```text
is_ai = true
enabled = true
next_action_at <= now
```

Process results in chunks.

Dispatch one AI decision job per due account.

The queued job should contain the AI user ID, not a serialized game-state graph.

## Explicit game progression

Before evaluating the AI:

```text
load AI user
    ↓
advance/synchronize state through existing game mechanisms
    ↓
load updated state
```

This makes AI accounts continue progressing without relying on human HTTP requests.

This mechanism needs to reuse `GlobalGame` behavior where possible rather than duplicating progression formulas.

## Concurrency

Only one decision job may control a particular AI player at a time.

Use Laravel locking/queue concurrency controls.

Possible events arriving simultaneously:

```text
scheduled tick
building completion
research completion
fleet return
```

must not produce concurrent conflicting AI decisions.

## Scheduling next action

After each run, calculate:

```text
next_action_at
```

based on useful future activity.

Examples:

If nothing can be afforded for 20 minutes:

```text
next_action_at ≈ affordability time
```

If research finishes in 45 minutes:

```text
next_action_at ≈ research completion
```

If nothing meaningful is known:

```text
use difficulty/activity interval + jitter
```

Do not wake every AI once per minute.

## Feature tests

Cover:

- due AI is queued
- future AI is ignored
- disabled AI is ignored
- normal user is ignored
- AI progresses even without HTTP traffic
- multiple scheduler passes do not duplicate work
- concurrent jobs cannot control same AI
- successful action sets next action
- failed action reschedules safely
- disabled AI stops progressing through AI jobs
- scheduling works for large AI populations
