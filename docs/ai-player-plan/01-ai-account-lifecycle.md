# Stage 1: AI Account Lifecycle

Create the minimum infrastructure required to manage AI accounts.

## AI account state

Store AI-specific state separately from ordinary user gameplay state.

Minimum MVP fields:

```text
user_id
enabled
personality
difficulty
next_action_at
last_action_at
```

The normal `users` record should contain or expose the reliable `is_ai` identity.

## Admin controls

The first admin implementation only needs:

```text
Enable AI
Disable AI
Set personality
Set difficulty
Set next action
```

More advanced controls can be added later.

## AI badge

Expose AI status to the player-facing UI by default.

The badge should be display logic only.

Do not modify usernames to prepend `[AI]` permanently.

## Feature tests

Cover:

- create AI user
- create normal user
- AI user has internal AI flag
- normal user does not
- enable AI
- disable AI
- badge visible by default
- badge can be hidden globally
- hidden badge does not affect scheduling
- deleting AI account prevents future execution
