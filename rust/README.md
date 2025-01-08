Rust is used for creating high-performance and memory-efficient that can be called
from PHP.

At the time of writing it is used for the BattleEngine in particular as battles with millions of units
take up a lot of memory and the BattleEngine in PHP can e.g. take up gigabytes of memory for a battle of 10M units.

## Rust source code
The rust source code is stored in the `./rust/` folder.

## Compile rust version
After making change to any rust modules, you should manually compile them and copy it over to
the `storage/ffi-libs` folder.

To compile rust, go to the rust module directory, e.g.

```bash
# Navigate to the rust package you want to compile
cd rust/test_ffi

# Compile the rust package
cargo build --release

# Copy the compiled rust package to the ffi-libs storage directory.
# The .so files are called by Laravel.
cp target/release/libtest_ffi.so ../../storage/ffi-libs
```

Alternatively, you can also run the `compile.sh` script inside the module's folder which executes the statements above:

```bash
# Navigate to the rust package you want to compile
cd rust/test_ffi

# Execute compile.sh
./compile.sh
```