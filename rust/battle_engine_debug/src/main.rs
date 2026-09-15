use battle_engine_ffi;
use serde_json::Result;

/// Debug driver for the battle engine.
///
/// Usage: battle_engine_debug <input.json> [iterations]
///
/// Runs the battle engine on the given input file. With iterations > 1 it
/// repeats the battle and reports the average time per battle, which is
/// useful for benchmarking and profiling (e.g. with perf).
///
/// Example input file content (current multi-fleet format):
///
/// {
///     "attacker_fleets": [{
///         "fleet_mission_id": 1,
///         "owner_id": 1,
///         "units": {
///             "204": {"unit_id": 204, "amount": 100, "attack_power": 50,
///                     "shield_points": 10, "hull_plating": 400,
///                     "rapidfire": {"210": 5, "212": 5}}
///         }
///     }],
///     "defender_fleets": [{
///         "fleet_mission_id": 2,
///         "owner_id": 2,
///         "units": {
///             "401": {"unit_id": 401, "amount": 100, "attack_power": 80,
///                     "shield_points": 20, "hull_plating": 200,
///                     "rapidfire": {}}
///         }
///     }]
/// }
fn main() -> Result<()> {
    let args: Vec<String> = std::env::args().collect();
    if args.len() < 2 {
        eprintln!("Usage: battle_engine_debug <input.json> [iterations]");
        std::process::exit(1);
    }
    let json_input = std::fs::read_to_string(&args[1]).expect("cannot read input file");
    let iterations: usize = if args.len() > 2 { args[2].parse().expect("invalid iteration count") } else { 1 };

    let c_input = std::ffi::CString::new(json_input).unwrap();

    let start = std::time::Instant::now();
    let mut last_output = String::new();
    for _ in 0..iterations {
        // Call the FFI interface directly, the same way the PHP client does.
        let output_ptr = battle_engine_ffi::fight_battle_rounds(c_input.as_ptr());
        last_output = unsafe {
            std::ffi::CStr::from_ptr(output_ptr).to_string_lossy().into_owned()
        };
        // Free the result via the engine's own allocator.
        battle_engine_ffi::free_battle_result(output_ptr);
    }
    let elapsed = start.elapsed();
    eprintln!(
        "{} iteration(s) in {:?} ({:.3} ms/battle)",
        iterations,
        elapsed,
        elapsed.as_secs_f64() * 1000.0 / iterations as f64
    );

    // Pretty print the JSON output of the last battle.
    let json: serde_json::Value = serde_json::from_str(&last_output)?;
    println!("{}", serde_json::to_string_pretty(&json).unwrap());

    Ok(())
}
