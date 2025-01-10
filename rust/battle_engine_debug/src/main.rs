use battle_engine_ffi;
use serde::Deserialize;
use serde_json::Result;

#[derive(Deserialize, Debug)]
struct Unit {
    unit_id: u32,
    structural_integrity: u32,
    shield_points: u32,
    attack_power: u32,
    original_shield_points: u32,
    current_shield_points: u32,
    current_hull_plating: u32,
    amount: u32,
}

fn main() -> Result<()> {
    let json_input = r#"
    {
        "attacker_units": [
            {
                "unit_id": "small_cargo",
                "structural_integrity": 4000,
                "shield_points": 10,
                "attack_power": 5,
                "original_shield_points": 10,
                "current_shield_points": 10,
                "current_hull_plating": 400,
                "amount": 5
            },
            {
                "unit_id": "light_fighter",
                "structural_integrity": 4000,
                "shield_points": 10,
                "attack_power": 50,
                "original_shield_points": 10,
                "current_shield_points": 10,
                "current_hull_plating": 400,
                "amount": 75
            }
        ],
        "defender_units": [
            {
                "unit_id": "rocket_launcher",
                "structural_integrity": 2000,
                "shield_points": 20,
                "attack_power": 80,
                "original_shield_points": 20,
                "current_shield_points": 20,
                "current_hull_plating": 200,
                "amount": 100
            }
        ]
    }
    "#;

    let input: battle_engine_ffi::BattleInput = serde_json::from_str(json_input)?;
    let output = battle_engine_ffi::process_battle_rounds(input);

    // Print the output for debugging
    println!("{:?}", output);

    Ok(())
}