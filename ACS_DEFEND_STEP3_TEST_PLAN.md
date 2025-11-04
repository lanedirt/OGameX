# ACS Defend Step 3 - Test Plan

## Prerequisites

Before testing, ensure you have:
- [ ] Two player accounts (or player + buddy)
- [ ] Players are buddies OR in same alliance
- [ ] At least one planet with some ships
- [ ] Some deuterium available

## Test 1: Basic Successful Dispatch ✅

**Goal:** Verify ACS Defend works with sufficient deuterium

**Setup:**
1. Log in as Player 1
2. Go to Fleet dispatch
3. Select a small fleet (e.g., 10 Light Fighters)

**Calculation:**
- 10 Light Fighters × 2 deut/hour = 20 deut/hour
- Hold time: 4 hours
- Total needed: 80 deuterium

**Steps:**
1. Select 10 Light Fighters
2. Select mission type: "ACS Defend" (mission 5)
3. Enter coordinates of buddy/alliance planet
4. Set hold time: 4 hours
5. Load at least 100 deuterium (more than needed)
6. Click "Send Fleet"

**Expected Result:**
- ✅ Fleet should dispatch successfully
- ✅ No error message
- ✅ Fleet appears in fleet events
- ✅ Mission shows as "ACS Defend"

**Check logs (if accessible):**
```bash
tail -f storage/logs/laravel.log | grep "ACS Defend"
```

Should see:
```
[DEBUG] ACS Defend deuterium validation passed
consumption_per_hour: 20.0
total_needed: 80.0
available: 100.0
hold_hours: 4
```

---

## Test 2: Insufficient Deuterium ❌

**Goal:** Verify validation prevents dispatch when not enough deuterium

**Setup:**
1. Select a larger fleet (e.g., 20 Cruisers)

**Calculation:**
- 20 Cruisers × 30 deut/hour = 600 deut/hour
- Hold time: 8 hours
- Total needed: 4,800 deuterium

**Steps:**
1. Select 20 Cruisers
2. Select mission type: "ACS Defend"
3. Enter coordinates of buddy/alliance planet
4. Set hold time: 8 hours
5. Load only 2,000 deuterium (less than needed)
6. Click "Send Fleet"

**Expected Result:**
- ❌ Error message should appear:
  ```
  Insufficient deuterium for hold duration.
  Required: 4,800.00 (600.00/hour × 8 hours).
  Available: 2,000.00
  ```
- ❌ Fleet NOT dispatched
- ❌ Ships remain on planet

---

## Test 3: Exact Amount Edge Case ⚖️

**Goal:** Verify fleet can be sent with exactly the required amount

**Setup:**
1. Select 5 Battleships

**Calculation:**
- 5 Battleships × 50 deut/hour = 250 deut/hour
- Hold time: 4 hours
- Total needed: 1,000 deuterium

**Steps:**
1. Select 5 Battleships
2. Select mission type: "ACS Defend"
3. Enter coordinates of buddy/alliance planet
4. Set hold time: 4 hours
5. Load EXACTLY 1,000 deuterium (not more, not less)
6. Click "Send Fleet"

**Expected Result:**
- ✅ Fleet should dispatch successfully
- ✅ Validation passes with exact amount

---

## Test 4: Maximum Hold Time (32 hours) 🕐

**Goal:** Verify maximum hold time works correctly

**Setup:**
1. Select 10 Heavy Fighters

**Calculation:**
- 10 Heavy Fighters × 7 deut/hour = 70 deut/hour
- Hold time: 32 hours (maximum)
- Total needed: 2,240 deuterium

**Steps:**
1. Select 10 Heavy Fighters
2. Select mission type: "ACS Defend"
3. Enter coordinates
4. Set hold time: 32 hours (should be max option)
5. Load at least 2,500 deuterium
6. Click "Send Fleet"

**Expected Result:**
- ✅ Fleet dispatches successfully with 32 hour hold
- ✅ Fleet events show correct return time (32 hours from arrival)

---

## Test 5: Low Consumption Ships (Probes) 🔬

**Goal:** Test with very low consumption rate

**Setup:**
1. Select 100 Espionage Probes

**Calculation:**
- 100 Espionage Probes × 0.1 deut/hour = 10 deut/hour
- Hold time: 16 hours
- Total needed: 160 deuterium

**Steps:**
1. Select 100 Espionage Probes
2. Select mission type: "ACS Defend"
3. Set hold time: 16 hours
4. Load 200 deuterium
5. Click "Send Fleet"

**Expected Result:**
- ✅ Fleet dispatches successfully
- ✅ Low consumption rate calculated correctly

---

## Test 6: Mixed Fleet 🚢

**Goal:** Test with multiple ship types

**Setup:**
1. Select mixed fleet:
   - 20 Light Fighters (40 deut/hour)
   - 10 Cruisers (300 deut/hour)
   - 5 Battleships (250 deut/hour)

**Calculation:**
- Total: 590 deut/hour
- Hold time: 8 hours
- Total needed: 4,720 deuterium

**Steps:**
1. Select the mixed fleet
2. Select mission type: "ACS Defend"
3. Set hold time: 8 hours
4. Load 5,000 deuterium
5. Click "Send Fleet"

**Expected Result:**
- ✅ Fleet dispatches successfully
- ✅ All ship types counted in consumption calculation

---

## Test 7: Non-Buddy/Alliance Target 🚫

**Goal:** Verify buddy/alliance restriction still works

**Steps:**
1. Select any fleet
2. Select mission type: "ACS Defend"
3. Enter coordinates of a player who is NOT buddy/alliance
4. Click "Send Fleet"

**Expected Result:**
- ❌ Error: Cannot send ACS Defend to non-buddy/alliance
- ❌ Mission should not be available OR fail validation

---

## Test 8: Own Planet 🏠

**Goal:** Verify cannot defend own planet

**Steps:**
1. Select any fleet
2. Select mission type: "ACS Defend"
3. Enter coordinates of YOUR OWN planet
4. Click "Send Fleet"

**Expected Result:**
- ❌ Mission type "ACS Defend" should not be available
- ❌ Or error message if attempted

---

## Database Verification

After successful dispatch, check the database:

```sql
-- Find the mission
SELECT id, mission_type, planet_id_from, planet_id_to, time_arrival, time_return
FROM fleet_missions
WHERE mission_type = 5
ORDER BY id DESC
LIMIT 1;

-- Check mission details
SELECT *
FROM fleet_missions
WHERE id = [MISSION_ID];
```

**Expected:**
- mission_type = 5
- time_arrival and time_return should be set
- Deuterium should be in mission cargo

---

## Common Issues & Solutions

### Issue: "ACS Defend" mission not showing
**Solution:**
- Verify target is buddy or alliance member
- Check that target planet exists
- Ensure you're not selecting your own planet

### Issue: Deuterium calculation seems wrong
**Solution:**
- Double-check ship types and counts
- Verify hold time setting
- Check consumption rates in `FleetHoldConsumptionService`

### Issue: Error message not showing
**Solution:**
- Check browser console (F12) for JavaScript errors
- Check Laravel logs: `storage/logs/laravel.log`
- Verify FleetController validation code is present

---

## Success Criteria

All tests pass if:
- ✅ Test 1: Successful dispatch with enough deuterium
- ✅ Test 2: Error message with insufficient deuterium
- ✅ Test 3: Exact amount works
- ✅ Test 4: Maximum hold time (32 hours) works
- ✅ Test 5: Low consumption ships work
- ✅ Test 6: Mixed fleet calculation correct
- ✅ Test 7: Non-buddy/alliance blocked
- ✅ Test 8: Own planet blocked

---

## After Testing

Once all tests pass, report results:
- Which tests passed ✅
- Which tests failed ❌
- Any unexpected behavior
- Error messages received
- Log entries observed

Then we can proceed to Step 4!
