# Stage 12: Optional Diplomacy

Diplomacy should be separate from the strategic adviser.

This is where conversational LLM functionality becomes useful.

Possible uses:

```text
player messages
alliances
negotiation
threats
agreements
personality
```

Conversation history is not game truth.

Structured database memory remains authoritative.

Player-written text must be treated as untrusted input.

No message should be able to instruct the AI to reveal hidden game state or execute an illegal action.

## Feature tests

Cover:

- conversation isolation
- prompt injection does not expose hidden state
- prompt injection cannot execute game action
- LLM disabled means diplomacy degrades safely
- rate limits prevent abuse
- conversation history persists correctly
