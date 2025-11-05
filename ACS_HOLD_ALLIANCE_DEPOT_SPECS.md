# ACS Hold (Defend) & Alliance Depot - Implementation Specifications

## Research Summary

### ACS Hold (ACS Defend) Mechanics

**Purpose:** Allow players to send fleets to defend allied planets/moons

**Key Features:**
- Mission type: "Hold" or "Defend"
- Can only deploy to alliance members or buddies
- Maximum 5 unique players can defend a single planet
- Maximum 16 fleets total (same as ACS Attack)
- Maximum hold duration: 32 hours
- Fleets consume deuterium per hour while holding
- If planet is attacked, all defending fleets participate in battle
- After battle, surviving fleets return to their origin planets

**Restrictions:**
- Same buddy/alliance restrictions as ACS Attack
- Must have enough deuterium for the entire hold duration
- Deuterium is consumed even if fleet is recalled early (non-refundable)

### Alliance Depot Building

**Purpose:** Supply deuterium to defending fleets to extend their hold time

**Mechanics:**
- Each level provides 20,000 deuterium per hour to orbiting fleets
- Deuterium is taken from planet owner's storage
- Extends the maximum hold time beyond what the fleet brought
- Only works for fleets in "Hold" mission at this planet

**Example:**
- Level 1 Depot = 20,000 deut/hour
- Level 2 Depot = 40,000 deut/hour
- Level 3 Depot = 60,000 deut/hour

## Deuterium Consumption Rates (per hour, per ship)

| Ship Type | Deuterium/Hour |
|-----------|----------------|
| Small Cargo Ship | 1 |
| Large Cargo Ship | 5 |
| Light Fighter | 2 |
| Heavy Fighter | 7 |
| Cruiser | 30 |
| Battleship | 50 |
| Colony Ship | 100 |
| Recycler | 30 |
| Espionage Probe | 0.1 |
| Bomber | 100 |
| Destroyer | 100 |
| Battlecruiser | 25 |
| Death Star | 0.1 |
| Solar Satellite | 0 (cannot be deployed) |

## Implementation Plan

### 1. Database Structure

#### Alliance Depot Building
- Add to buildings table/system
- Building machine name: `alliance_depot`
- Max level: Configurable (typically 12-15)
- Requirements: Research/building dependencies

#### ACS Hold Groups
Similar to ACS Attack groups but for defense:
- Table: `acs_defend_groups` (or reuse `acs_groups` with type field)
- Fields:
  - `planet_id` - Target planet being defended
  - `arrival_time` - When fleets arrive
  - `hold_until_time` - When fleets return (max 32 hours after arrival)
  - `status` - pending/active/completed

### 2. Mission Type

**New Mission:** Hold/Defend (mission_type = ?)
- Check next available mission type number
- Similar to deployment but with time limit and deut consumption

### 3. Deuterium Consumption System

**Hourly Tick:**
- Cron job runs every hour (or continuous calculation on arrival)
- For each active "Hold" fleet:
  - Calculate total deuterium needed (ships × consumption rate)
  - Check if planet has enough deuterium (own fleet + depot supply)
  - Deduct deuterium from:
    1. Fleet's own cargo first
    2. Alliance Depot supply (if available)
    3. Planet owner's deuterium storage
  - If insufficient deuterium: Fleet auto-recalls immediately

**Calculation Example:**
```
Fleet composition:
- 100 Light Fighters = 100 × 2 = 200 deut/hour
- 50 Cruisers = 50 × 30 = 1,500 deut/hour
- 10 Battleships = 10 × 50 = 500 deut/hour
Total: 2,200 deuterium/hour

Hold duration: 10 hours
Total deuterium needed: 22,000

If Alliance Depot Level 2: 40,000 deut/hour available
Fleet can hold for full 10 hours using depot supply
```

### 4. Battle Integration

When planet is attacked:
- Load all "Hold" fleets at target planet
- Combine with planet owner's defenses and stationed fleet
- Run battle engine with all defenders vs attackers
- After battle:
  - Surviving defending fleets return to origin
  - Each fleet gets proportional debris field share
  - Battle report sent to all participants

### 5. Fleet Dispatch UI

**Fleet Mission Selection:**
- Add "Hold" mission option
- Available only when:
  - Target is buddy or alliance member
  - Target is not your own planet
  - Player has required research (ACS tech level 1)

**Hold Duration Input:**
- Slider or input field
- Min: 1 hour
- Max: 32 hours
- Calculate and display total deuterium cost

**Validation:**
- Check buddy/alliance relationship
- Check fleet capacity for deuterium
- Check max 5 players / 16 fleets limit
- Display warning if deuterium insufficient

### 6. Alliance Depot UI

**Building Page:**
- Standard building upgrade interface
- Show current supply rate: "Provides X deuterium/hour to defending fleets"
- Show deuterium consumption of current defending fleets

**Planet Overview:**
- Show active defending fleets (if any)
- Show deuterium being consumed per hour
- Show time until depot storage runs out

### 7. Fleet Events Display

**Outgoing Hold Mission:**
- Show countdown to arrival
- Show countdown to return
- Show deuterium consumption rate
- Show "Recall" button

**Incoming Hold Mission (Planet Owner View):**
- Show allied fleets in orbit
- Show who sent them
- Show ship counts
- Show when they will return
- Show total deuterium consumption

## Testing Checklist

### ACS Hold Basic Functionality
- [ ] Can send fleet on Hold mission to buddy
- [ ] Can send fleet on Hold mission to alliance member
- [ ] Cannot send Hold to non-buddy/non-alliance
- [ ] Cannot exceed 5 player limit
- [ ] Cannot exceed 16 fleet limit
- [ ] Fleet arrives at target planet
- [ ] Fleet holds for specified duration
- [ ] Fleet returns after hold duration
- [ ] Deuterium is consumed per hour

### Alliance Depot
- [ ] Building can be constructed
- [ ] Depot supplies deuterium to holding fleets
- [ ] Deuterium deducted from planet storage
- [ ] Fleet recalls if deuterium runs out
- [ ] Higher depot levels supply more deuterium

### Battle Integration
- [ ] Defending fleets participate in battle
- [ ] All defenders get battle report
- [ ] Surviving fleets return to origin
- [ ] Debris field generated correctly
- [ ] Battle report shows all participants

### Edge Cases
- [ ] Fleet recalls if planet runs out of deuterium
- [ ] Fleet recalls correctly with remaining ships
- [ ] Multiple fleets from same player
- [ ] Maximum hold time (32 hours) enforced
- [ ] Depot doesn't affect non-Hold fleets

## Configuration Values

Recommended config settings:
```php
'acs' => [
    'max_players' => 5,
    'max_fleets' => 16,
    'max_hold_hours' => 32,
],
'alliance_depot' => [
    'deuterium_per_level' => 20000, // per hour
    'max_level' => 12,
],
```

## Mission Type Assignment

Need to check existing mission types and assign:
- Mission Type X = ACS Hold/Defend

Current known types:
- 1 = Attack
- 2 = ACS Attack
- 3 = Transport
- 4 = Deploy
- 5 = ACS Defend (to be implemented)
- etc.
