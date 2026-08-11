# Stage 8: Rust AI Decision Engine

Rust should now be introduced based on actual profiling or the need for deeper calculations.

The MVP should already expose a language-neutral contract:

```text
AiState + CandidateActions
    ↓
AiDecisionEngine
    ↓
AiDecision
```

So Laravel can initially use:

```text
LaravelUtilityDecisionEngine
```

and later:

```text
RustDecisionEngine
```

without changing scheduling, perception or action execution.

## Rust responsibilities

Good candidates for Rust:

```text
large candidate scoring
target scoring
risk calculations
fleet composition evaluation
battle simulations
planning/search
forward simulations
batch decisions
```

Not good candidates:

```text
Eloquent
queues
database persistence
server configuration
memory storage
domain-service execution
```

## Current Rust compatibility

Follow the existing OGameX Rust build/deployment model.

Keep AI Rust code inside the existing workspace.

Introduce:

```text
ai_engine_core
ai_engine_ffi
```

The FFI layer should remain thin.

Unlike the MVP, Rust now receives the same immutable AI snapshot Laravel already uses.

## Hardened FFI contract

The AI FFI should include:

```text
versioned input schema
versioned output schema
explicit error envelope
deterministic seed
explicit returned-memory release
panic containment
```

Rust should never mutate OGameX state.

## Feature tests

PHP feature tests must verify:

- Laravel and Rust engines accept same decision contract
- valid state crosses FFI correctly
- malformed Rust response is rejected
- Rust unavailable has controlled behavior
- same seed/state produces deterministic result
- unknown candidate returned by Rust is rejected
- normal domain service still executes final action

Rust also gets its own:

```text
unit tests
property tests
benchmarks
schema tests
```
