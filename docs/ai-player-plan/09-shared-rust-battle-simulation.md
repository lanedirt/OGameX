# Stage 9: Shared Rust Battle Simulation

Before doing large-scale military planning, separate reusable battle calculations from the FFI boundary.

Conceptually:

```text
battle_engine_core
    ↑             ↑
battle FFI     AI engine
```

The AI engine can then run deterministic battle simulations directly inside Rust without Rust calling Rust through FFI.

This enables:

```text
fleet composition testing
attack outcome estimation
expected losses
expected profit
Monte Carlo evaluation
```

Normal real battles still follow normal OGameX execution.

Simulated battles never alter game state.

## Feature tests

Cover:

- simulation cannot mutate real fleet
- simulated result affects target score
- real attack still executes through normal service
- deterministic simulation works with supplied seed
