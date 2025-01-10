use serde::{Deserialize, Serialize};
use std::ffi::{CStr, CString};
use std::os::raw::c_char;
use rand::Rng;
use std::collections::HashMap;

#[derive(Debug, Serialize, Deserialize, Clone)]
struct BattleUnit {
    unit_id: String,
    structural_integrity: f64,
    shield_points: f64,
    attack_power: f64,
    original_shield_points: f64,
    current_shield_points: f64,
    current_hull_plating: f64,
}

#[derive(Debug, Serialize, Deserialize, Clone)]
struct BattleRound {
    attacker_ships: Vec<BattleUnit>,
    defender_ships: Vec<BattleUnit>,
    attacker_losses: Vec<BattleUnit>,
    defender_losses: Vec<BattleUnit>,
    attacker_losses_in_round: Vec<BattleUnit>,
    defender_losses_in_round: Vec<BattleUnit>,
    absorbed_damage_attacker: f64,
    absorbed_damage_defender: f64,
    full_strength_attacker: f64,
    full_strength_defender: f64,
    hits_attacker: i32,
    hits_defender: i32,
}

#[derive(Serialize, Deserialize)]
pub struct BattleInput {
    attacker_units: Vec<BattleUnit>,
    defender_units: Vec<BattleUnit>,
}

#[derive(Debug, Serialize, Deserialize)]
pub struct BattleOutput {
    rounds: Vec<BattleRound>,
}

#[no_mangle]
pub extern "C" fn fight_battle_rounds(input_json: *const c_char) -> *mut c_char {
    let input_str = unsafe { CStr::from_ptr(input_json).to_str().unwrap() };
    let battle_input: BattleInput = serde_json::from_str(input_str).unwrap();
    let battle_output = process_battle_rounds(battle_input);
    let result_json = serde_json::to_string(&battle_output).unwrap();
    let c_str = CString::new(result_json).unwrap();
    c_str.into_raw()
}

fn expand_units(units: &Vec<BattleUnit>) -> Vec<BattleUnit> {
    let mut expanded = Vec::new();
    for unit in units {
        let count = unit.structural_integrity as i32;
        for _ in 0..count {
            expanded.push(BattleUnit {
                unit_id: unit.unit_id.clone(),
                structural_integrity: 1.0,
                shield_points: unit.shield_points,
                attack_power: unit.attack_power,
                original_shield_points: unit.original_shield_points,
                current_shield_points: unit.shield_points,
                current_hull_plating: unit.current_hull_plating,
            });
        }
    }
    expanded
}

fn compress_units(units: &Vec<BattleUnit>) -> Vec<BattleUnit> {
    let mut unit_counts: HashMap<String, (BattleUnit, i32)> = HashMap::new();

    for unit in units {
        if unit.current_hull_plating > 0.0 {
            unit_counts.entry(unit.unit_id.clone())
                .and_modify(|(_, count)| *count += 1)
                .or_insert((unit.clone(), 1));
        } else {
            // Ensure destroyed units are included with count 0
            unit_counts.entry(unit.unit_id.clone())
                .or_insert((unit.clone(), 0));
        }
    }

    unit_counts.into_iter()
        .map(|(_, (mut unit, count))| {
            unit.structural_integrity = count as f64;
            unit
        })
        .collect()
}

pub fn process_battle_rounds(input: BattleInput) -> BattleOutput {
    let mut rounds = Vec::new();

    // Expand units into individual ships
    let mut attacker_units = expand_units(&input.attacker_units);
    let mut defender_units = expand_units(&input.defender_units);

    // Create initial round
    let mut initial_round = BattleRound {
        attacker_ships: input.attacker_units.clone(),
        defender_ships: input.defender_units.clone(),
        attacker_losses: Vec::new(),
        defender_losses: Vec::new(),
        attacker_losses_in_round: Vec::new(),
        defender_losses_in_round: Vec::new(),
        absorbed_damage_attacker: 0.0,
        absorbed_damage_defender: 0.0,
        full_strength_attacker: 0.0,
        full_strength_defender: 0.0,
        hits_attacker: 0,
        hits_defender: 0,
    };
    rounds.push(initial_round);

    // Fight up to 6 rounds
    for _ in 0..6 {
        if attacker_units.is_empty() || defender_units.is_empty() {
            break;
        }

        let mut round = BattleRound {
            attacker_ships: Vec::new(),
            defender_ships: Vec::new(),
            attacker_losses: rounds.last().unwrap().attacker_losses.clone(),
            defender_losses: rounds.last().unwrap().defender_losses.clone(),
            attacker_losses_in_round: Vec::new(),
            defender_losses_in_round: Vec::new(),
            absorbed_damage_attacker: 0.0,
            absorbed_damage_defender: 0.0,
            full_strength_attacker: 0.0,
            full_strength_defender: 0.0,
            hits_attacker: 0,
            hits_defender: 0,
        };

        // Process combat
        process_combat(&mut attacker_units, &mut defender_units, &mut round, true);
        process_combat(&mut defender_units, &mut attacker_units, &mut round, false);

        // Update round statistics
        round.attacker_ships = compress_units(&attacker_units);
        round.defender_ships = compress_units(&defender_units);

        // Calculate losses
        calculate_losses(&mut round, &input.attacker_units, &input.defender_units);

        rounds.push(round);
    }

    BattleOutput { rounds }
}

fn process_combat(
    attackers: &mut Vec<BattleUnit>,
    defenders: &mut Vec<BattleUnit>,
    round: &mut BattleRound,
    is_attacker: bool,
) {
    let mut rng = rand::thread_rng();

    attackers.retain(|unit| {
        if defenders.is_empty() {
            return true;
        }

        let target_idx = rng.gen_range(0..defenders.len());
        let target = &mut defenders[target_idx];

        let damage = unit.attack_power;

        if damage < (0.01 * target.current_shield_points) {
            return true;
        }

        let mut shield_absorption = 0.0;

        if target.current_shield_points > 0.0 {
            if damage <= target.current_shield_points {
                shield_absorption = damage;
                target.current_shield_points -= damage;
            } else {
                shield_absorption = target.current_shield_points;
                target.current_hull_plating -= damage - target.current_shield_points;
                target.current_shield_points = 0.0;
            }
        } else {
            target.current_hull_plating -= damage;
        }

        // Update statistics
        if is_attacker {
            round.hits_attacker += 1;
            round.full_strength_attacker += damage;
            round.absorbed_damage_defender += shield_absorption;
        } else {
            round.hits_defender += 1;
            round.full_strength_defender += damage;
            round.absorbed_damage_attacker += shield_absorption;
        }

        true
    });

    // Remove destroyed defenders
    defenders.retain(|unit| unit.current_hull_plating > 0.0);
}

fn calculate_losses(
    round: &mut BattleRound,
    initial_attacker: &Vec<BattleUnit>,
    initial_defender: &Vec<BattleUnit>,
) {
    // Calculate losses by comparing current counts with initial counts
    for unit in initial_attacker {
        let current_count = round.attacker_ships.iter()
            .find(|u| u.unit_id == unit.unit_id)
            .map(|u| u.structural_integrity)
            .unwrap_or(0.0);

        let initial_count = unit.structural_integrity;

        if current_count < initial_count {
            let mut loss_unit = unit.clone();
            loss_unit.structural_integrity = initial_count - current_count;
            round.attacker_losses_in_round.push(loss_unit);
        }
    }

    // Do the same for defender
    for unit in initial_defender {
        let current_count = round.defender_ships.iter()
            .find(|u| u.unit_id == unit.unit_id)
            .map(|u| u.structural_integrity)
            .unwrap_or(0.0);

        let initial_count = unit.structural_integrity;

        if current_count < initial_count {
            let mut loss_unit = unit.clone();
            loss_unit.structural_integrity = initial_count - current_count;
            round.defender_losses_in_round.push(loss_unit);
        }
    }

    // Update total losses
    round.attacker_losses.extend(round.attacker_losses_in_round.clone());
    round.defender_losses.extend(round.defender_losses_in_round.clone());
}
