use battle_engine_ffi;
use serde::Deserialize;
use serde_json::Result;
use std::collections::HashMap;

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
    rapidfire: HashMap<u32, u32>,
}

fn main() -> Result<()> {

    /*
    Input with defender units without rapidfire.
    {"attacker_units":[{"unit_id":206,"amount":30,"shield_points":50,"attack_power":400,"hull_plating":2700,"rapidfire":{"210":5,"212":5,"204":6,"401":10}}],"defender_units":[{"unit_id":401,"amount":500,"shield_points":20,"attack_power":80,"hull_plating":200,"rapidfire":[]}]}

    Example input:
     {
        "attacker_units": [
            {
                "unit_id": 202,
                "amount": 5,
                "shield_points": 10,
                "attack_power": 5,
                "hull_plating": 400,
                "rapidfire": {
                    "210": 5,
                    "212": 5
                }
            },
            {
                "unit_id": 204,
                "amount": 75,
                "shield_points": 10,
                "attack_power": 50,
                "hull_plating": 400,
                "rapidfire": {
                    "210": 5,
                    "212": 5
                }
            }
        ],
        "defender_units": [
            {
                "unit_id": 401,
                "amount": 100,
                "shield_points": 20,
                "attack_power": 80,
                "hull_plating": 200,
                "rapidfire": {}
            }
        ]
    }

    This has caused eternal loop before:
    {"attacker_units":[{"unit_id":204,"amount":5000,"shield_points":10,"attack_power":50,"hull_plating":400,"rapidfire":{"210":5,"212":5}}],"defender_units":[{"unit_id":408,"amount":1,"shield_points":10000,"attack_power":1,"hull_plating":10000,"rapidfire":{}}]}
     */



    let json_input = r#"
{"attacker_units":[{"unit_id":206,"amount":700000,"shield_points":100,"attack_power":800,"hull_plating":5400,"rapidfire":{"210":5,"212":5,"204":6,"401":10}},{"unit_id":207,"amount":100000,"shield_points":400,"attack_power":2000,"hull_plating":12000,"rapidfire":{"210":5,"212":5}}],"defender_units":[{"unit_id":401,"amount":100000,"shield_points":40,"attack_power":160,"hull_plating":400,"rapidfire":{}},{"unit_id":406,"amount":20000,"shield_points":600,"attack_power":6000,"hull_plating":20000,"rapidfire":{}}]}"#;

    let input: battle_engine_ffi::BattleInput = serde_json::from_str(json_input)?;
    let output = battle_engine_ffi::process_battle_rounds(input);

    // Print the output as pretty JSON
    println!("{}", serde_json::to_string_pretty(&output).unwrap());

    Ok(())
}