# Stage 5: Observations and Structured Memory

Once the MVP works, introduce memory before advanced military behavior.

Memory should be structured and stored in the database.

## Observations

Examples:

```text
espionage report
attack received
successful raid
failed raid
combat loss
player interaction
colony event
```

Each observation should contain:

```text
AI player
subject
type
source
timestamp
confidence
payload
expiry
```

## Relationships

Aggregate repeated observations into per-player knowledge.

Examples:

```text
threat
hostility
profitability
relative strength estimate
attacks received
successful raids
failed raids
last seen
last attacked
last probed
```

## Memory decay

Historical information should not stay perfectly accurate forever.

Confidence decays with age.

For example:

```text
fresh espionage report
    ↓
high confidence

hours later
    ↓
medium confidence

much later
    ↓
stale
```

The AI should never magically receive updated enemy values.

## Feature tests

Cover:

- observation creation
- ownership isolation
- stale observation behavior
- relationship aggregation
- confidence decay
- expired memory
- repeated raids update profitability
- repeated attacks update threat
- one AI cannot access another AI's memory
