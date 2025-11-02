# ACS Defend Implementation - Step 2 Complete ✅

## What Was Implemented

### Fleet Hold Consumption Service
**File:** `app/Services/FleetHoldConsumptionService.php`

A complete service for calculating deuterium consumption while fleets are in ACS Defend (hold) position.

### Ship Consumption Rates

All ship types now have hourly deuterium consumption rates (per ship):

| Ship Type | Consumption/Hour |
|-----------|------------------|
| Small Cargo | 1 |
| Large Cargo | 5 |
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
| Deathstar | 0.1 |
| Solar Satellite | 0 (cannot be deployed) |

### Service Methods

**1. `calculateConsumptionPerHour(UnitCollection $units)`**
- Calculates deuterium consumed per hour for a given fleet
- Returns: float (total deuterium/hour)

**Example:**
```php
// Fleet: 100 Light Fighters + 50 Cruisers
// Consumption: (100 × 2) + (50 × 30) = 200 + 1,500 = 1,700 deut/hour
```

**2. `calculateTotalConsumption(UnitCollection $units, int $hours)`**
- Calculates total deuterium needed for specific duration
- Returns: float (total deuterium needed)

**Example:**
```php
// Same fleet holding for 10 hours
// Total: 1,700 × 10 = 17,000 deuterium
```

**3. `calculateMaxHoldTime(UnitCollection $units, float $availableDeuterium)`**
- Calculates maximum hours fleet can hold with available deuterium
- Returns: int (max hours, capped at 32)

**Example:**
```php
// Fleet consuming 1,700/hour with 10,000 deuterium available
// Max time: 10,000 / 1,700 = 5 hours (floored)
```

**4. `hasEnoughDeuterium(UnitCollection $units, int $hours, float $availableDeuterium)`**
- Validates if fleet has enough deuterium for hold duration
- Returns: bool

**5. `getConsumptionRate(string $machineName)`**
- Gets consumption rate for a specific ship type
- Returns: float

## Testing Step 2

### Test 1: Basic Consumption Calculation

```php
php artisan tinker

use OGame\Services\FleetHoldConsumptionService;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\GameObjects\Models\Units\Unit;

$service = new FleetHoldConsumptionService();

// Create a test fleet
$units = new UnitCollection();

// Add 100 Light Fighters (2 deut/hour each)
$lightFighter = // Get light fighter object
$units->addUnit(new Unit($lightFighter, 100));

// Add 10 Cruisers (30 deut/hour each)
$cruiser = // Get cruiser object
$units->addUnit(new Unit($cruiser, 10));

// Calculate consumption per hour
$perHour = $service->calculateConsumptionPerHour($units);
echo "Consumption per hour: $perHour\n";
// Expected: 200 + 300 = 500

// Calculate total for 8 hours
$total = $service->calculateTotalConsumption($units, 8);
echo "Total for 8 hours: $total\n";
// Expected: 500 × 8 = 4,000

// Calculate max hold time with 10,000 deuterium
$maxHours = $service->calculateMaxHoldTime($units, 10000);
echo "Max hold time: $maxHours hours\n";
// Expected: 10,000 / 500 = 20 hours
```

### Test 2: Individual Ship Rates

```php
$service = new FleetHoldConsumptionService();

echo "Small Cargo: " . $service->getConsumptionRate('small_cargo') . " deut/hour\n";
// Expected: 1

echo "Battleship: " . $service->getConsumptionRate('battleship') . " deut/hour\n";
// Expected: 50

echo "Deathstar: " . $service->getConsumptionRate('deathstar') . " deut/hour\n";
// Expected: 0.1
```

### Test 3: Deuterium Validation

```php
$service = new FleetHoldConsumptionService();

// Fleet consuming 500/hour
$has Enough = $service->hasEnoughDeuterium($units, 10, 5000);
echo "Has enough for 10 hours: " . ($hasEnough ? "YES" : "NO") . "\n";
// Expected: YES (500 × 10 = 5,000)

$hasEnough = $service->hasEnoughDeuterium($units, 10, 4999);
echo "Has enough with 4,999: " . ($hasEnough ? "YES" : "NO") . "\n";
// Expected: NO (need 5,000)
```

### Test 4: Edge Cases

```php
// Test with 0 consumption ships
$units = new UnitCollection();
$satellite = // Get solar satellite
$units->addUnit(new Unit($satellite, 1000));

$consumption = $service->calculateConsumptionPerHour($units);
echo "Satellite consumption: $consumption\n";
// Expected: 0

$maxHours = $service->calculateMaxHoldTime($units, 1000);
echo "Max hold time with no consumption: $maxHours\n";
// Expected: 32 (capped at max)
```

## What's NOT Yet Implemented

- ❌ Fleet dispatch validation (check deuterium before sending)
- ❌ Hourly deuterium deduction during hold
- ❌ Alliance Depot supply integration
- ❌ Auto-recall when deuterium runs out
- ❌ Battle integration

## Next Steps

**Step 3** will add:
1. Validation in `FleetController` to check deuterium before dispatch
2. Display of consumption rate and total cost in UI
3. Prevention of dispatch if insufficient deuterium

## Files Created

1. ✅ `app/Services/FleetHoldConsumptionService.php` - NEW

## Calculation Examples

### Example 1: Small Defense Fleet
- 50 Light Fighters = 50 × 2 = 100 deut/hour
- 10 Cruisers = 10 × 30 = 300 deut/hour
- **Total:** 400 deut/hour
- **For 16 hours:** 6,400 deuterium needed

### Example 2: Large Defense Fleet
- 100 Heavy Fighters = 100 × 7 = 700 deut/hour
- 50 Cruisers = 50 × 30 = 1,500 deut/hour
- 20 Battleships = 20 × 50 = 1,000 deut/hour
- **Total:** 3,200 deut/hour
- **For 32 hours (max):** 102,400 deuterium needed

### Example 3: Minimal Consumption
- 1,000 Espionage Probes = 1,000 × 0.1 = 100 deut/hour
- **For 32 hours:** 3,200 deuterium needed

## Ready for Step 3?

Once verified, we proceed to:
**Step 3: Fleet Dispatch Validation**

This will add:
- Pre-dispatch deuterium check
- UI display of consumption rates
- Error messages for insufficient deuterium
