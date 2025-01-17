use battle_engine_ffi;
use serde_json::Result;

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

    10M units battle for memory usage debug:
    {"attacker_units":[{"unit_id":204,"amount":5000000,"shield_points":10,"attack_power":50,"hull_plating":400,"rapidfire":{"210":5,"212":5}}],"defender_units":[{"unit_id":401,"amount":5000000,"shield_points":20,"attack_power":80,"hull_plating":200,"rapidfire":{}}]}

     */

    let json_input = r#"
    {"attacker_units":[{"unit_id":204,"amount":4500000,"shield_points":10,"attack_power":50,"hull_plating":400,"rapidfire":{"210":5,"212":5}}],"defender_units":[{"unit_id":401,"amount":1,"shield_points":20,"attack_power":80,"hull_plating":200,"rapidfire":{}}]}
"#;

    let input: battle_engine_ffi::BattleInput = serde_json::from_str(json_input)?;
    let output = battle_engine_ffi::process_battle_rounds(input);

    // Print the output as pretty JSON
    println!("{}", serde_json::to_string_pretty(&output).unwrap());

    Ok(())
}