# Compile the rust package
cargo build --manifest-path="rust/test_ffi/Cargo.toml" --release

# Copy the compiled rust package to the ffi-libs storage directory.
# The .so files are called by Laravel.
cp rust/test_ffi/target/release/libtest_ffi.so storage/ffi-libs