# OGameX AI Player Implementation Plan — Context

This document contains the shared context, core rules, and cross-cutting concerns that apply across all implementation stages.

---

## Direction

The AI player should be implemented as a normal OGameX account controlled by an internal decision engine.

The initial implementation should focus on a small playable vertical slice:

```text
AI flagged user
    ↓
explicit AI tick
    ↓
load current game state
    ↓
economy/research decision
    ↓
existing OGameX domain services
    ↓
next_action_at
```

The MVP should stay entirely inside Laravel.

Rust, strategic planning, memory, combat intelligence and LLM advising remain part of the architecture, but are introduced only after the Laravel-based MVP is working and measurable.

---

## Core rules

These rules apply from the first implementation.

### AI accounts are normal players

An AI player:

- has a normal `User`
- owns normal planets
- has normal resources
- has normal research
- uses normal queues
- waits normal build/research times
- follows normal fleet restrictions
- follows normal game configuration
- cannot directly modify resources or game state

AI actions must execute through the same domain services used by human players.

Examples include:

```text
PlayerService
PlanetService
BuildingQueueService
ResearchQueueService
UnitQueueService
FleetMissionService
```

The AI decision layer decides **what it wants to do**.

OGameX remains responsible for deciding **whether that action is legal and executing it**.

---

## Server configuration

AI must be completely optional.

Default configuration:

```text
AI players: OFF
LLM strategic advisor: OFF
AI badge: ON
```

Server admins should eventually be able to configure:

```text
Enable AI players
Maximum AI player count
AI difficulty
AI activity frequency
AI personalities
LLM advising
LLM budget
Visible AI badge
```

No existing OGameX server should change behavior simply because the AI feature exists.

---

## AI account identification

Add an explicit internal AI flag.

Conceptually:

```text
is_ai = true
```

This should not be inferred from username, email, role names or naming conventions.

The flag is needed for:

- scheduling
- admin tools
- metrics
- filtering
- ranking behavior
- AI management
- migrations
- testing

AI accounts should be visually identifiable by default.

For example:

```text
[AI] PlayerName
```

or a dedicated badge.

The badge should be controlled separately from the internal flag.

Example server setting:

```text
show_ai_badge = true
```

Hiding the badge must never change `is_ai`.

The internal identity must always remain reliable.

---

## Game progression and explicit AI ticks

OGameX currently progresses parts of game state through `GlobalGame` during requests.

AI accounts cannot depend on human traffic.

An AI player may need to progress even when:

```text
no human players are online
no page requests occur
```

Therefore AI requires an explicit scheduling mechanism.

Each AI account should have:

```text
next_action_at
```

The scheduler selects only AI accounts where:

```text
is_ai = true
enabled = true
next_action_at <= now
```

Before making a decision, the AI tick must ensure the relevant player's game state has been progressed using the same underlying game progression mechanisms used normally.

The AI must not implement its own separate resource clock.

Conceptually:

```text
AiTick
    ↓
advance/synchronize player state
    ↓
build current state
    ↓
make decision
    ↓
execute through normal service
    ↓
calculate next_action_at
```

This also makes AI activity independent from HTTP traffic.

---

## Testing policy

Every player-facing AI capability must have feature-test coverage.

The goal is:

> 100% feature/requirement coverage, with every behavior mapped to one or more feature tests.

This should not be confused with forcing every internal Rust branch or PHP helper to be tested through HTTP-level feature tests.

Use the correct test layer:

```text
Laravel feature tests
    ↓
all gameplay behavior and integration

PHP unit tests
    ↓
small deterministic application logic

Rust unit/property tests
    ↓
engine invariants and algorithms

Rust benchmarks
    ↓
performance regression

LLM fakes
    ↓
all normal CI
```

No live AI provider should be required by CI.

---

## MVP test matrix

The first PR series should not be considered complete until these categories are covered.

### Configuration

- off by default
- enabled server
- AI limit
- badge on/off

### Account identity

- reliable `is_ai`
- normal user unaffected
- disabled AI unaffected by scheduler

### Tick/progression

- AI state progresses without human request
- correct due scheduling
- concurrency protection
- next action scheduling

### State

- resources
- planet buildings
- queues
- research
- legal candidates

### Economy

- mine upgrades
- energy handling
- affordability
- storage behavior

### Research

- prerequisites
- affordability
- queue restrictions
- personality influence

### Execution

- domain service used
- normal costs
- normal durations
- stale decision rejected
- invalid decision rejected
- no direct state mutation

---

## MVP release boundary

This is where I would stop the first implementation.

The MVP contains:

```text
AI flagged accounts
server-level enable/disable
visible AI badge
admin limits
next_action_at
explicit AI ticks
Laravel queue scheduling
economy decisions
research decisions
existing OGameX services
feature tests
```

It does NOT require:

```text
Rust AI engine
LLM
memory
combat AI
espionage
diplomacy
GOAP
neural networks
vector database
```

Those remain part of the planned architecture.

The MVP answers the most important questions first:

```text
Can AI accounts progress without human requests?

Can they use normal OGameX services?

Can many accounts be scheduled cheaply?

Does the decision abstraction work?

Does the feature remain completely optional?
```

If those fail, adding Rust or an LLM would not solve the underlying problem.

---

## Recommended PR sequence

### PR 1
AI account identity, feature configuration and visible badge.

### PR 2
AI scheduler, explicit game tick, `next_action_at`, queue and locking.

### PR 3
AI state builder and legal economy/research candidates.

### PR 4
Laravel utility-based economy and research decisions.

At this point the **MVP is complete**.

Then continue:

### PR 5
Structured observations and memory.

### PR 6
Expansion and colonization.

### PR 7
Espionage and target intelligence.

### PR 8
Military/defensive behavior.

### PR 9
Rust AI engine behind existing decision interface.

### PR 10
Shared Rust battle simulation and deeper tactical evaluation.

### PR 11
Bounded strategic planner.

### PR 12
Optional Laravel AI strategic advisor.

### PR 13
Optional diplomacy.

### PR 14
Large-server optimization, Redis/Horizon profile and load benchmarks.

### PR 15+
Simulator, evolutionary tuning and experimental learned models.

---

## MVP definition of done

The first milestone should be deliberately narrow:

> An administrator can enable AI players, create or flag normal OGameX accounts as AI, and those accounts can continue progressing without human HTTP traffic by periodically making economy and research decisions through the same services and rules used by human players.

It must also satisfy:

```text
off by default
AI accounts reliably tagged
visible badge by default
badge configurable
normal accounts unaffected
explicit tick works without humans online
no direct game-state mutation
no HTTP/browser automation
no Rust requirement
no LLM requirement
all MVP behaviors covered by feature tests
```

This gives us a working product slice first.

Everything after that builds on the same boundaries rather than replacing the MVP architecture.
