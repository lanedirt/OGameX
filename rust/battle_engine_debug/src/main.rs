use battle_engine_ffi;
use serde_json::Result;
use libc;

fn main() -> Result<()> {

    /*
    Input with defender units without rapidfire.
    {"attacker_units":{"206": {"unit_id":206,"amount":30,"shield_points":50,"attack_power":400,"hull_plating":2700,"rapidfire":{"210":5,"212":5,"204":6,"401":10}}},"defender_units":{"401": {"unit_id":401,"amount":500,"shield_points":20,"attack_power":80,"hull_plating":200,"rapidfire":{}}}}

    Example input:
     {
        "attacker_units": {
            "202": {
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
            "204": {
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
        },
        "defender_units": {
            "401": {
                "unit_id": 401,
                "amount": 100,
                "shield_points": 20,
                "attack_power": 80,
                "hull_plating": 200,
                "rapidfire": {}
            }
        }
    }

    This has caused eternal loop before:
    {"attacker_units":{"204": {"unit_id":204,"amount":5000,"shield_points":10,"attack_power":50,"hull_plating":400,"rapidfire":{"210":5,"212":5}}},"defender_units":{"408": {"unit_id":408,"amount":1,"shield_points":10000,"attack_power":1,"hull_plating":10000,"rapidfire":{}}}}

    10M units battle for memory usage debug:
    {"attacker_units":{"204": {"unit_id":204,"amount":5000000,"shield_points":10,"attack_power":50,"hull_plating":400,"rapidfire":{"210":5,"212":5}}},"defender_units":{"401": {"unit_id":401,"amount":5000000,"shield_points":20,"attack_power":80,"hull_plating":200,"rapidfire":{}}}}

     */

    let json_input = r#"
    {"attacker_units":{"204": {"unit_id":204,"amount":100000,"shield_points":10,"attack_power":50,"hull_plating":400,"rapidfire":{"210":5,"212":5}}},"defender_units":{"401": {"unit_id":401,"amount":100000,"shield_points":20,"attack_power":80,"hull_plating":200,"rapidfire":{}}}}
"#;

    // Convert input string to CString for FFI call
    let c_input = std::ffi::CString::new(json_input).unwrap();

    // Call the FFI interface directly
    let output_ptr = battle_engine_ffi::fight_battle_rounds(c_input.as_ptr());

    // Convert output back to string and free memory
    let output = unsafe {
        let output_str = std::ffi::CStr::from_ptr(output_ptr).to_string_lossy().into_owned();
        libc::free(output_ptr as *mut libc::c_void);
        output_str
    };

    // Pretty print the JSON output
    let json: serde_json::Value = serde_json::from_str(&output)?;
    println!("{}", serde_json::to_string_pretty(&json).unwrap());

    Ok(())
}