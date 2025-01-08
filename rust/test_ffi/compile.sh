# Compile the rust package
cargo build --release

# Copy the compiled rust package to the ffi-libs storage directory.
# The .so files are called by Laravel.
cp target/release/libtest_ffi.so ../../storage/ffi-libs