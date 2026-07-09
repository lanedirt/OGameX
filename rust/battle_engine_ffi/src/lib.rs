//! # Battle Engine FFI
//!
//! `battle_engine_ffi` is the Rust implementation of the OGameX battle engine.
//!
//! This Rust library is called from the PHP client RustBattleEngine.php via FFI (Foreign Function Interface)
//! and takes the battle input in JSON, processes the battle rounds and returns the battle output in JSON.
//!
//! This battle engine is functionally equivalent to the OGameX PHP battle engine but is optimized
//! for performance and memory usage.
//!
//! # Multi-Attacker Support
//! This engine supports multiple attacker fleets (ACS Attack) and multiple defender fleets (ACS Defend).
//! Each fleet's units are tracked with their fleet_mission_id and owner_id, allowing for accurate
//! per-fleet result reporting.
//!
//! # Engine v2 design
//! All static unit values are resolved once per battle into a flat `Vec<CombatUnitInfo>` with one
//! entry per (fleet, unit type) pair. Each individual unit instance carries only a `u16` index into
//! that array plus its current shield/hull, so the combat hot loop performs direct slice indexing
//! instead of per-shot HashMap lookups. Rapidfire chances are precomputed into a cross table
//! (attacker info index x defender info index) using the exact same formula previously evaluated
//! per shot. Round statistics are derived from maintained per-type alive counters instead of
//! rescanning all unit instances every round.
//!
//! Keeping unit values per (fleet, unit type) also fixes a v1 bug where metadata of fleets sharing
//! the same unit type was merged with last-insert-wins semantics: in ACS battles with differing
//! tech levels, all units of a shared type silently fought with one (arbitrary) fleet's values.
//!
//! # Combat fidelity (verified against the original OGame v0.84 battle engine source)
//! - Shield damage is applied in whole multiples of 1% of the unit's max shield. Shots weaker
//!   than 1% of max shield therefore deplete nothing (the classic "bounce" rule), but they only
//!   bounce while the shield is up: once the shield is stripped within a round, every shot
//!   damages the hull in full.
//! - Units fire in deterministic order: fleets in input (slot) order, unit types within a fleet
//!   by ascending unit id. This makes ACS shield-stripping tactics work like the original game
//!   (e.g. one fleet's destroyers break a Deathstar's shield so another fleet's light fighters
//!   no longer bounce in the same round).
//! - Rapidfire is rolled after every shot, including bounced shots and shots wasted on units
//!   already destroyed this round, with probability (n - 1) / n.
use serde::{Deserialize, Serialize};
use std::ffi::{CStr, CString};
use std::os::raw::c_char;
use rand::Rng;
use std::collections::HashMap;
#[cfg(feature = "memory-metrics")]
use memory_stats::memory_stats;

/// Battle input which is provided by the PHP client.
#[derive(Serialize, Deserialize)]
pub struct BattleInput {
    attacker_fleets: Vec<FleetInput>,
    defender_fleets: Vec<FleetInput>,
}

/// Input structure for a single fleet (attacker or defender).
#[derive(Serialize, Deserialize, Clone)]
struct FleetInput {
    fleet_mission_id: u32,
    owner_id: u32,
    units: HashMap<i16, BattleUnitInfo>,
}

/// Battle unit info which is provided by the PHP client.
///
/// This contains static information about the input units and their amount.
#[derive(Serialize, Deserialize, Clone)]
struct BattleUnitInfo {
    unit_id: i16,
    amount: u32,
    attack_power: f32,
    shield_points: f32,
    hull_plating: f32,
    rapidfire: HashMap<i16, u16>,
}

/// Battle unit count to keep track of the amount of units of a certain type.
#[derive(Serialize, Deserialize, Clone)]
struct BattleUnitCount {
    unit_id: i16,
    amount: u32,
}

/// Static combat values for one unit type within one fleet.
///
/// One entry exists per (fleet, unit type) pair. Individual units reference their
/// entry by index, so all hot-loop lookups are direct slice accesses.
struct CombatUnitInfo {
    unit_id: i16,
    fleet_mission_id: u32,
    owner_id: u32,
    /// Amount of units of this type the fleet started the battle with.
    amount_start: u32,
    attack_power: f32,
    shield_points: f32,
    hull_plating: f32,
    /// Precomputed rapidfire chance against each opposing unit info (indexed by the
    /// opposing side's info index). NaN means no rapidfire against that unit type.
    /// The chance is (n - 1) / n for a rapidfire amount of n, matching the original game.
    rapidfire_chance: Vec<f64>,
}

/// A single unit instance and its current health during battle.
///
/// `info` indexes into the side's `Vec<CombatUnitInfo>`.
struct CombatUnit {
    info: u16,
    shield: f32,
    hull: f32,
}

/// Battle round which is used to keep track of the battle statistics for a single round.
#[derive(Serialize, Deserialize)]
struct BattleRound {
    /// The units of the attacker remaining at the end of the round.
    attacker_ships: HashMap<i16, BattleUnitCount>,
    /// The units of the defender remaining at the end of the round.
    defender_ships: HashMap<i16, BattleUnitCount>,
    /// Unit losses of the attacker until now which includes previous rounds.
    attacker_losses: HashMap<i16, BattleUnitCount>,
    /// Unit losses of the defender until now which includes previous rounds.
    defender_losses: HashMap<i16, BattleUnitCount>,
    /// Unit losses of the attacker in this round.
    attacker_losses_in_round: HashMap<i16, BattleUnitCount>,
    /// Unit losses of the defender in this round.
    defender_losses_in_round: HashMap<i16, BattleUnitCount>,
    /// Total amount of damage absorbed by the attacker this round.
    absorbed_damage_attacker: f64,
    /// Total amount of damage absorbed by the defender this round.
    absorbed_damage_defender: f64,
    /// Total amount of full strength of the attacker at the start of the round.
    full_strength_attacker: f64,
    /// Total amount of full strength of the defender at the start of the round.
    full_strength_defender: f64,
    /// Total amount of hits the attacker made this round.
    hits_attacker: u32,
    /// Total amount of hits the defender made this round.
    hits_defender: u32,
    /// Per-fleet attacker results keyed by fleet_mission_id.
    attacker_fleet_results: HashMap<u32, FleetResult>,
    /// Per-fleet defender results keyed by fleet_mission_id.
    defender_fleet_results: HashMap<u32, FleetResult>,
}

impl BattleRound {
    fn new() -> Self {
        BattleRound {
            attacker_ships: HashMap::new(),
            defender_ships: HashMap::new(),
            attacker_losses: HashMap::new(),
            defender_losses: HashMap::new(),
            attacker_losses_in_round: HashMap::new(),
            defender_losses_in_round: HashMap::new(),
            absorbed_damage_attacker: 0.0,
            absorbed_damage_defender: 0.0,
            full_strength_attacker: 0.0,
            full_strength_defender: 0.0,
            hits_attacker: 0,
            hits_defender: 0,
            attacker_fleet_results: HashMap::new(),
            defender_fleet_results: HashMap::new(),
        }
    }
}

/// Result for a single fleet (attacker or defender).
#[derive(Serialize, Deserialize, Clone)]
struct FleetResult {
    fleet_mission_id: u32,
    owner_id: u32,
    units_start: HashMap<i16, BattleUnitCount>,
    units_result: HashMap<i16, BattleUnitCount>,
    units_lost: HashMap<i16, BattleUnitCount>,
}

/// Memory metrics which is used to keep track of the peak memory usage during the battle.
///
/// This is only used for debugging purposes and not actually consumed by the PHP client.
#[derive(Serialize, Deserialize)]
struct MemoryMetrics {
    peak_memory: u64, // in kilobytes
}

/// Battle output which is returned to the PHP client.
///
/// This contains the battle statistics and memory metrics. Memory metrics are only used
/// for debugging purposes when called from battle_engine_debug Rust project.
#[derive(Serialize, Deserialize)]
pub struct BattleOutput {
    rounds: Vec<BattleRound>,
    memory_metrics: MemoryMetrics,
}

/// FFI interface to process the battle rounds and return the battle output.
///
/// This is the method which is called from the PHP client in RustBattleEngine.php.
#[no_mangle]
pub extern "C" fn fight_battle_rounds(input_json: *const c_char) -> *mut c_char {
    let input_str = unsafe { CStr::from_ptr(input_json).to_str().unwrap() };
    let battle_input: BattleInput = serde_json::from_str(input_str).unwrap();
    let battle_output = process_battle_rounds(battle_input);
    let result_json = serde_json::to_string(&battle_output).unwrap();
    let c_str = CString::new(result_json).unwrap();
    c_str.into_raw()
}

/// Free a battle result string previously returned by `fight_battle_rounds`.
///
/// The result is allocated by Rust and must be freed by Rust. Callers (PHP FFI)
/// must invoke this after copying the result string, otherwise every battle
/// leaks its output buffer for the lifetime of the process.
#[no_mangle]
pub extern "C" fn free_battle_result(ptr: *mut c_char) {
    if !ptr.is_null() {
        unsafe {
            let _ = CString::from_raw(ptr);
        }
    }
}

/// Process the battle rounds and return the battle output.
fn process_battle_rounds(input: BattleInput) -> BattleOutput {
    let mut peak_memory = 0;
    let mut rounds: Vec<BattleRound> = Vec::new();

    // Resolve all static unit values once. Rapidfire chances are precomputed
    // against the opposing side's unit infos.
    let attacker_infos = build_combat_infos(&input.attacker_fleets, &input.defender_fleets);
    let defender_infos = build_combat_infos(&input.defender_fleets, &input.attacker_fleets);

    // Create the individual unit instances from the per-type amounts.
    let mut attacker_units = expand_units(&attacker_infos);
    let mut defender_units = expand_units(&defender_infos);

    // Alive counters per info index, kept in sync with the unit vectors so the
    // per-round statistics never have to rescan all instances.
    let mut attacker_alive: Vec<u32> = attacker_infos.iter().map(|info| info.amount_start).collect();
    let mut defender_alive: Vec<u32> = defender_infos.iter().map(|info| info.amount_start).collect();

    // Track peak memory usage for debugging purposes
    update_peak_memory(&mut peak_memory);

    // Fight up to 6 rounds
    for _ in 0..6 {
        if attacker_units.is_empty() || defender_units.is_empty() {
            break;
        }

        let mut round = BattleRound::new();

        // Process combat
        process_combat(&attacker_units, &mut defender_units, &attacker_infos, &defender_infos, &mut round, true);
        process_combat(&defender_units, &mut attacker_units, &defender_infos, &attacker_infos, &mut round, false);

        // Remove destroyed units, record per-round losses and regenerate shields.
        cleanup_units(&mut attacker_units, &attacker_infos, &mut attacker_alive, &mut round.attacker_losses_in_round);
        cleanup_units(&mut defender_units, &defender_infos, &mut defender_alive, &mut round.defender_losses_in_round);

        // Update round statistics from the alive counters.
        round.attacker_ships = count_ships(&attacker_infos, &attacker_alive);
        round.defender_ships = count_ships(&defender_infos, &defender_alive);
        round.attacker_losses = count_losses(&attacker_infos, &attacker_alive);
        round.defender_losses = count_losses(&defender_infos, &defender_alive);
        round.attacker_fleet_results = fleet_results(&attacker_infos, &attacker_alive);
        round.defender_fleet_results = fleet_results(&defender_infos, &defender_alive);

        rounds.push(round);

        // Track peak memory usage for debugging purposes
        update_peak_memory(&mut peak_memory);
    }

    BattleOutput {
        rounds,
        memory_metrics: MemoryMetrics {
            peak_memory,
        },
    }
}

/// Resolve the static combat values of all fleets on one side into a flat array with
/// one entry per (fleet, unit type) pair.
///
/// The rapidfire chance of each unit is precomputed against every unit info of the
/// opposing side so the hot loop can look it up by index. The chance value is derived
/// with the same formula the engine previously evaluated per shot:
/// 100 - floor((100 / amount) * 100) / 100. NaN marks "no rapidfire against this type".
fn build_combat_infos(own_fleets: &[FleetInput], opposing_fleets: &[FleetInput]) -> Vec<CombatUnitInfo> {
    // Collect the opposing unit ids in info order to index the rapidfire table.
    let mut opposing_unit_ids: Vec<i16> = Vec::new();
    for fleet in opposing_fleets {
        for unit in sorted_fleet_units(fleet) {
            opposing_unit_ids.push(unit.unit_id);
        }
    }

    let mut infos = Vec::new();
    for fleet in own_fleets {
        for unit in sorted_fleet_units(fleet) {
            let rapidfire_chance = opposing_unit_ids.iter().map(|opposing_id| {
                match unit.rapidfire.get(opposing_id) {
                    Some(rapidfire_amount) => {
                        // Rapidfire continues with probability (n - 1) / n, matching the
                        // original game. For example:
                        // - rapidfire amount of 4 means 3/4 = 75% chance.
                        // - rapidfire amount of 10 means 9/10 = 90% chance.
                        (*rapidfire_amount as f64 - 1.0) / *rapidfire_amount as f64
                    }
                    None => f64::NAN,
                }
            }).collect();

            infos.push(CombatUnitInfo {
                unit_id: unit.unit_id,
                fleet_mission_id: fleet.fleet_mission_id,
                owner_id: fleet.owner_id,
                amount_start: unit.amount,
                attack_power: unit.attack_power,
                shield_points: unit.shield_points,
                hull_plating: unit.hull_plating,
                rapidfire_chance,
            });
        }
    }
    infos
}

/// Return the units of a fleet ordered by ascending unit id.
///
/// Units fire in this deterministic order, matching the original game where unit
/// types shoot in fixed positional order. Combined with fleets firing in input
/// (slot) order this makes ACS firing order reproducible.
fn sorted_fleet_units(fleet: &FleetInput) -> Vec<&BattleUnitInfo> {
    let mut units: Vec<&BattleUnitInfo> = fleet.units.values().collect();
    units.sort_by_key(|unit| unit.unit_id);
    units
}

/// Expand the unit infos into individual unit instances.
fn expand_units(infos: &[CombatUnitInfo]) -> Vec<CombatUnit> {
    let total: u32 = infos.iter().map(|info| info.amount_start).sum();
    let mut units = Vec::with_capacity(total as usize);
    for (idx, info) in infos.iter().enumerate() {
        for _ in 0..info.amount_start {
            units.push(CombatUnit {
                info: idx as u16,
                shield: info.shield_points,
                hull: info.hull_plating,
            });
        }
    }
    units
}

/// Simulates combat for a single round between two groups of units.
///
/// # Why:
/// This function handles the core mechanics of combat by calculating damage, updating
/// unit health, and determining if a unit can attack again (via rapidfire). It also
/// updates statistics for the battle round to reflect the results.
///
/// # Parameters:
/// - `attackers`: Units attacking in this phase.
/// - `defenders`: Units being attacked in this phase.
/// - `attacker_infos`: Static values of the attacking units (attack power, rapidfire, etc.).
/// - `defender_infos`: Static values of the defending units (max shield, max hull, etc.).
/// - `round`: Stores round statistics, such as hits and absorbed damage.
/// - `is_attacker`: Whether the current phase is attacker-to-defender or vice versa.
fn process_combat(
    attackers: &[CombatUnit],
    defenders: &mut Vec<CombatUnit>,
    attacker_infos: &[CombatUnitInfo],
    defender_infos: &[CombatUnitInfo],
    round: &mut BattleRound,
    is_attacker: bool,
) {
    let mut rng = rand::thread_rng();

    for attacker in attackers.iter() {
        // Get the static values of the attacking unit.
        let attacker_info = &attacker_infos[attacker.info as usize];
        let damage = attacker_info.attack_power;

        loop {
            // Select a random defender as a target
            let target_idx = rng.gen_range(0..defenders.len());
            let target = &mut defenders[target_idx];

            // Get the static values of the defending unit.
            let target_info = &defender_infos[target.info as usize];

            let mut shield_absorption = 0.0;
            let mut bounced = false;
            if target.hull <= 0.0 {
                // Target was already destroyed earlier this round: the shot is wasted.
                // It still counts towards the round statistics and still rolls rapidfire.
            } else if target.shield <= 0.0 {
                // Shield is stripped: full damage goes to the hull, however weak the shot.
                if damage >= target.hull {
                    target.hull = 0.0;
                } else {
                    target.hull -= damage;
                }
            } else {
                // Shield damage is dealt in whole multiples of 1% of the max shield.
                // A shot weaker than 1% of max shield depletes zero multiples: it is
                // fully absorbed without effect (the classic "bounce" rule).
                let shield_percentile = 0.01 * target_info.shield_points;
                let depleted_percentiles = (damage / shield_percentile).floor();
                if target.shield < depleted_percentiles * shield_percentile {
                    // Shield breaks: the overflow beyond the remaining shield hits the hull.
                    shield_absorption = target.shield;
                    let hull_damage = damage - target.shield;
                    if hull_damage >= target.hull {
                        target.hull = 0.0;
                    } else {
                        target.hull -= hull_damage;
                    }
                    target.shield = 0.0;
                } else {
                    // Shot fully absorbed; the shield only loses whole percentiles.
                    target.shield -= depleted_percentiles * shield_percentile;
                    shield_absorption = damage;
                    bounced = depleted_percentiles == 0.0;
                }
            }

            // If hull integrity < 70%, then unit can explode randomly. Roll dice to see if it does.
            // Bounced shots never trigger the roll, otherwise massed weak shots could explode a
            // damaged unit straight through an intact shield.
            if !bounced && target.hull > 0.0 && target.hull / target_info.hull_plating < 0.7 {
                let explosion_chance = 100.0 - ((target.hull / target_info.hull_plating) * 100.0);
                let roll = rng.gen_range(0..=100);
                if roll < explosion_chance as i32 {
                    // Unit explodes, set current hull plating and shield points to 0.
                    target.hull = 0.0;
                    target.shield = 0.0;
                }
            }

            // Update round statistics for hits and damage absorbed
            if is_attacker {
                round.hits_attacker += 1;
                round.full_strength_attacker += damage as f64;
                round.absorbed_damage_defender += shield_absorption as f64;
            } else {
                round.hits_defender += 1;
                round.full_strength_defender += damage as f64;
                round.absorbed_damage_attacker += shield_absorption as f64;
            }

            // Check if the current unit has rapidfire against the target unit. If so, then
            // roll dice to see if the current unit can attack again. Like the original game
            // this roll happens after every shot, including bounced and wasted ones.
            let rapidfire_chance = attacker_info.rapidfire_chance[target.info as usize];
            if rapidfire_chance.is_nan() {
                break;
            }

            // Roll for rapidfire. If the roll is below the rapidfire chance the unit
            // attacks again and the loop continues.
            let roll = rng.gen_range(0.0..1.0);
            if roll >= rapidfire_chance {
                break;
            }
        }
    }
}

/// Clean up one side's units after all units have attacked each other.
///
/// This method handles:
/// - Removing destroyed units and keeping the per-type alive counters in sync.
/// - Recording the losses of this round.
/// - Applying shield regeneration for the surviving units.
fn cleanup_units(
    units: &mut Vec<CombatUnit>,
    infos: &[CombatUnitInfo],
    alive: &mut [u32],
    losses_in_round: &mut HashMap<i16, BattleUnitCount>,
) {
    // Remove destroyed units and regenerate the shields of the survivors in a
    // single sweep, so the unit vector is only traversed once per round.
    units.retain_mut(|unit| {
        // Check if unit is fully destroyed.
        if unit.hull <= 0.0 {
            let info = &infos[unit.info as usize];
            alive[unit.info as usize] -= 1;
            increment_battle_unit_count_amount(losses_in_round, info.unit_id, 1);
            return false;
        }

        unit.shield = infos[unit.info as usize].shield_points;
        true
    });
}

/// Build the remaining-ships map (per unit type, summed across fleets) from the alive counters.
fn count_ships(infos: &[CombatUnitInfo], alive: &[u32]) -> HashMap<i16, BattleUnitCount> {
    let mut ships: HashMap<i16, BattleUnitCount> = HashMap::new();
    for (idx, info) in infos.iter().enumerate() {
        if alive[idx] > 0 {
            increment_battle_unit_count_amount(&mut ships, info.unit_id, alive[idx]);
        }
    }
    ships
}

/// Build the accumulated losses map (per unit type, summed across fleets) by comparing
/// the alive counters with the starting amounts.
fn count_losses(infos: &[CombatUnitInfo], alive: &[u32]) -> HashMap<i16, BattleUnitCount> {
    let mut losses: HashMap<i16, BattleUnitCount> = HashMap::new();
    for (idx, info) in infos.iter().enumerate() {
        let lost = info.amount_start - alive[idx];
        if lost > 0 {
            increment_battle_unit_count_amount(&mut losses, info.unit_id, lost);
        }
    }
    losses
}

/// Build the per-fleet results (units at battle start, units remaining, units lost)
/// from the alive counters.
fn fleet_results(infos: &[CombatUnitInfo], alive: &[u32]) -> HashMap<u32, FleetResult> {
    let mut results: HashMap<u32, FleetResult> = HashMap::new();
    for (idx, info) in infos.iter().enumerate() {
        let result = results.entry(info.fleet_mission_id).or_insert_with(|| FleetResult {
            fleet_mission_id: info.fleet_mission_id,
            owner_id: info.owner_id,
            units_start: HashMap::new(),
            units_result: HashMap::new(),
            units_lost: HashMap::new(),
        });

        result.units_start.insert(info.unit_id, BattleUnitCount {
            unit_id: info.unit_id,
            amount: info.amount_start,
        });
        if alive[idx] > 0 {
            result.units_result.insert(info.unit_id, BattleUnitCount {
                unit_id: info.unit_id,
                amount: alive[idx],
            });
        }
        let lost = info.amount_start - alive[idx];
        if lost > 0 {
            result.units_lost.insert(info.unit_id, BattleUnitCount {
                unit_id: info.unit_id,
                amount: lost,
            });
        }
    }
    results
}

/// Helper method to increment the amount property of a BattleUnitCount struct.
fn increment_battle_unit_count_amount(hash_map: &mut HashMap<i16, BattleUnitCount>, unit_id: i16, amount_to_increment: u32) {
    let count = hash_map.entry(unit_id).or_insert(BattleUnitCount {
        unit_id,
        amount: 0,
    });
    count.amount += amount_to_increment;
}

/// Update the peak memory usage statistics. Only used for debugging purposes.
///
/// Gated behind the `memory-metrics` feature: memory_stats() reads /proc/self/smaps
/// which the kernel generates by walking all process memory mappings. That makes
/// every battle progressively slower in long-running processes, so production
/// builds skip it entirely and report a peak of 0.
#[cfg(feature = "memory-metrics")]
fn update_peak_memory(current_peak: &mut u64) {
    if let Some(usage) = memory_stats() {
        *current_peak = (*current_peak).max(usage.physical_mem as u64 / 1024);
    }
}

#[cfg(not(feature = "memory-metrics"))]
fn update_peak_memory(_current_peak: &mut u64) {}
