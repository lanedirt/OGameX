# Stage 0: Architecture and Feature Boundaries

This stage establishes the contracts without implementing sophisticated AI.

## Define the core concepts

Introduce clear concepts for:

```text
AiPlayer
AiPersonality
AiState
AiCandidateAction
AiDecision
AiDecisionEngine
AiActionExecutor
```

Keep them independent from any particular AI technique.

The system should allow future implementations such as:

```text
LaravelUtilityDecisionEngine
RustDecisionEngine
PlannerDecisionEngine
LearnedDecisionEngine
```

without changing normal OGameX domain services.

## Feature switches

Add configuration for:

```text
ai.enabled
ai.show_badge
ai.max_players
```

Future configuration may include:

```text
ai.llm.enabled
ai.engine
ai.difficulty
```

but those do not need to be implemented in the MVP.

## Acceptance criteria

Feature tests must prove:

- AI is disabled by default
- normal servers behave exactly as before
- only `is_ai` users can be scheduled
- hiding the AI badge does not remove AI identity
- normal users are never treated as AI
- disabling AI prevents AI jobs from being dispatched
