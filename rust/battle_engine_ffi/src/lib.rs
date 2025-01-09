use serde::{Deserialize, Serialize};
use std::ffi::{CStr, CString};
use std::os::raw::c_char;
use rand::Rng;

#[derive(Serialize, Deserialize)]
struct BattleUnit {
    unit_id: String,
    structural_integrity: f64,
    shield_points: f64,
    attack_power: f64,
    original_shield_points: f64,
    current_shield_points: f64,
    current_hull_plating: f64,
}

#[derive(Serialize, Deserialize)]
struct BattleRound {
    attacker_ships: Vec<String>,
    defender_ships: Vec<String>,
    attacker_losses: Vec<String>,
    defender_losses: Vec<String>,
    attacker_losses_in_round: Vec<String>,
    defender_losses_in_round: Vec<String>,
    absorbed_damage_attacker: f64,
    absorbed_damage_defender: f64,
    full_strength_attacker: f64,
    full_strength_defender: f64,
    hits_attacker: i32,
    hits_defender: i32,
}

#[derive(Serialize, Deserialize)]
struct BattleInput {
    attacker_units: Vec<BattleUnit>,
    defender_units: Vec<BattleUnit>,
}

#[derive(Serialize, Deserialize)]
struct BattleOutput {
    rounds: Vec<BattleRound>,
}

#[no_mangle]
pub extern "C" fn fight_battle_rounds(input_json: *const c_char) -> *mut c_char {
    // Convert C string to Rust string
    let input_str = unsafe { CStr::from_ptr(input_json).to_str().unwrap() };

    // Parse input JSON
    let battle_input: BattleInput = serde_json::from_str(input_str).unwrap();

    // Process battle rounds
    let battle_output = process_battle_rounds(battle_input);

    // Convert result to JSON string
    let result_json = serde_json::to_string(&battle_output).unwrap();

    // Convert to C string and return
    let c_str = CString::new(result_json).unwrap();
    c_str.into_raw()
}

fn process_battle_rounds(input: BattleInput) -> BattleOutput {
    let mut rounds = Vec::new();
    let mut attacker_units = input.attacker_units;
    let mut defender_units = input.defender_units;

    // Fight up to 6 rounds
    for _ in 0..6 {
        if attacker_units.is_empty() || defender_units.is_empty() {
            break;
        }

        let mut round = BattleRound {
            attacker_ships: Vec::new(),
            defender_ships: Vec::new(),
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

        // Process attacks
        process_attacks(&mut attacker_units, &mut defender_units, &mut round, true);
        process_attacks(&mut defender_units, &mut attacker_units, &mut round, false);

        // Update round statistics
        update_round_stats(&mut round, &attacker_units, &defender_units);

        rounds.push(round);
    }

    BattleOutput { rounds }
}

fn process_attacks(
    attackers: &mut Vec<BattleUnit>,
    defenders: &mut Vec<BattleUnit>,
    round: &mut BattleRound,
    is_attacker: bool,
) {
    let mut rng = rand::thread_rng();

    for attacker in attackers.iter_mut() {
        loop {
            if defenders.is_empty() {
                break;
            }

            let target_idx = rng.gen_range(0..defenders.len());
            let defender = &mut defenders[target_idx];

            // Apply damage
            let damage = attacker.attack_power;

            if damage < (0.01 * defender.current_shield_points) {
                break;
            }

            let mut shield_absorption = 0.0;

            if defender.current_shield_points > 0.0 {
                if damage <= defender.current_shield_points {
                    shield_absorption = damage;
                    defender.current_shield_points -= damage;
                } else {
                    shield_absorption = defender.current_shield_points;
                    defender.current_hull_plating -= damage - defender.current_shield_points;
                    defender.current_shield_points = 0.0;
                }
            } else {
                defender.current_hull_plating -= damage;
            }

            // Update round statistics
            if is_attacker {
                round.hits_attacker += 1;
                round.full_strength_attacker += damage;
                round.absorbed_damage_defender += shield_absorption;
            } else {
                round.hits_defender += 1;
                round.full_strength_defender += damage;
                round.absorbed_damage_attacker += shield_absorption;
            }

            // TODO: Implement rapidfire logic
            break;
        }
    }
}

fn update_round_stats(
    round: &mut BattleRound,
    attacker_units: &Vec<BattleUnit>,
    defender_units: &Vec<BattleUnit>,
) {
    // Update ships remaining
    round.attacker_ships = attacker_units.iter()
        .map(|u| u.unit_id.clone())
        .collect();

    round.defender_ships = defender_units.iter()
        .map(|u| u.unit_id.clone())
        .collect();

    // TODO: Update losses statistics
}
