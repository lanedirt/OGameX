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
                "unit_id": 1,
                "amount": 5,
                "shield_points": 10,
                "attack_power": 5,
                "hull_plating": 400
            },
            {
                "unit_id": 2,
                "amount": 75,
                "shield_points": 10,
                "attack_power": 50,
                "hull_plating": 400
            }
        ],
        "defender_units": [
            {
                "unit_id": 100,
                "amount": 100,
                "shield_points": 20,
                "attack_power": 80,
                "hull_plating": 200
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