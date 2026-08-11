# Stage 3: MVP Perception and Current-State Builder

The MVP does not need enemy memory or espionage yet.

It only needs enough information for economic and research decisions.

Build an immutable AI state from the AI player's own legal information.

Example areas:

```text
resources
energy
planet buildings
building queue
research
research queue
shipyard where relevant
game configuration
current time
```

The AI decision engine should not receive Eloquent models.

Instead:

```text
OGameX state
    ↓
AiStateFactory
    ↓
AiState
```

This prepares the architecture for Rust later.

## Candidate generation

Laravel should generate legal candidate actions.

For the MVP:

```text
build building
start research
```

Possibly later:

```text
build units
fleet mission
```

The decision engine should choose among candidates that OGameX already considers potentially valid.

Execution still performs final validation through the existing service.

## Feature tests

Cover:

- AI sees its own resources correctly
- AI sees building levels correctly
- AI sees current research correctly
- queued building is represented correctly
- queued research is represented correctly
- legal buildings appear as candidates
- illegal buildings do not
- legal research appears
- unmet prerequisites exclude research
- resource restrictions are represented
- queue restrictions are represented
