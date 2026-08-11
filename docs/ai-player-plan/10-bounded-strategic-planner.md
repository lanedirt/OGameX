# Stage 10: Bounded Strategic Planner

Utility AI is good for:

```text
what is valuable now?
```

Planning handles:

```text
how do I reach a multi-step goal?
```

Example:

```text
Goal:
colonize another planet

Plan:
research Astrophysics
save resources
build Colony Ship
choose position
send mission
```

Planner execution must be bounded.

Configuration should limit:

```text
maximum nodes
maximum depth
maximum simulations
```

If planning fails or exceeds budget:

```text
fallback to normal utility AI
```

Every planned action still requires normal OGameX validation before execution.

## Feature tests

Cover:

- valid multi-step plan
- already satisfied prerequisites skipped
- impossible plan returns safely
- state change invalidates plan
- bounded search limit respected
- planner cannot execute illegal action
- fallback works
