Rust is used for creating high-performance and memory-efficient libraries that can be called from PHP.

At the time of writing Rust is used for the BattleEngine in particular as battles with millions of units take up a lot of memory and take a long time to process in PHP.

## Rust source code
All rust source code is stored in the `./rust/` folder.

## Making changes
If you want to make changes to the Rust code, you can do so by editing the files in the `./rust/` folder. E.g. the Rust BattleEngine logic is located in `./rust/battle_engine_ffi/src/lib.rs`. After making changes to this file you can compile the Rust code by running the `compile.sh` script inside the rust directory.

> Note: The Rust code is compiled automatically during the Docker container startup, so no Rust compiled code is committed to the repository. You only have to run the `compile.sh` script if you want to compile the Rust code manually during development and testing. Checking in the new Rust source code is enough.

```bash
# Execute compile.sh (from app root) within ogamex-app Docker container.
./rust/compile.sh
```

Alternatively, you can compile the Rust code manually by running the following commands inside the `./rust/` directory:

```bash
# Navigate to the rust directory
cd rust
# Compile the rust packages
cargo build --release
# Copy the compiled rust package to the storage/rust-libs directory.
# These .so files are called by Laravel.
cp target/release/lib*_ffi.so ../../storage/rust-libs
```

## Debugging Rust code
In order to debug the Rust code you can manually execute the `battle_engine_debug` Rust package. This calls the Rust BattleEngine with an example fleet and prints the result to the console.

Run the debug rust package via the following command:

```bash
# Navigate to the rust directory
cd rust
# Execute the debug package
cargo run --release --package battle_engine_debug
```

You can also use a proper Rust IDE such as JetBrains RustRover (free for non-commercial use) to aid in debugging by adding breakpoints to the Rust code.

## Profiling PHP and Rust BattleEngines
The speed differences between the PHP and Rust BattleEngines can be profiled with these commands:

```bash
# Execute the PHP BattleEngine performance test via Laravel command.
php artisan test:battle-engine-performance php --fleet='{"attacker":{"cruiser":700000,"battle_ship":100000},"defender":{"plasma_turret":20000,"rocket_launcher":100000}}'

# Execute the Rust BattleEngine performance test via Laravel command.
php artisan test:battle-engine-performance rust --fleet='{"attacker":{"cruiser":700000,"battle_ship":100000},"defender":{"plasma_turret":20000,"rocket_launcher":100000}}'
```

Example output from commands above which shows that the Rust BattleEngine is approximately 200x faster at time of writing (2025-01-18):

```bash
# PHP BattleEngine
Execution time: 53,951.76ms
Peak PHP memory usage: 226.01MB

# Rust BattleEngine
Execution time: 175.61ms
Peak PHP memory usage: 34.00MB
+ Rust memory usage through FFI: +/- 18.90MB
= Total memory usage: 54.90MB
```