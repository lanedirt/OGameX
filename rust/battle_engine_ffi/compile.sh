# Compile the rust package
cargo build --manifest-path="rust/battle_engine_ffi/Cargo.toml" --release

# Copy the compiled rust package to the ffi-libs storage directory.
# The .so files are called by Laravel.
cp rust/battle_engine_ffi/target/release/libbattle_engine_ffi.so storage/ffi-libs
