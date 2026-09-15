#!/bin/sh

# Compile the production battle engine library in its own cargo invocation.
# A workspace-wide build would unify features across all member crates, so any
# debug/test crate enabling e.g. memory-metrics would silently compile that
# feature into the production .so as well.
echo "Compiling battle_engine_ffi (production)..."
if ! cargo build "--manifest-path=rust/Cargo.toml" "--release" "-p" "battle_engine_ffi"; then
    echo "ERROR: Rust compilation failed!"
    exit 1
fi

# Copy the compiled rust libraries to the storage/rust-libs directory.
# The .so files are called by Laravel. Copy before building the rest of the
# workspace so the production artifact cannot be overwritten by a
# feature-unified build.
if [ -f rust/target/release/libbattle_engine_ffi.so ]; then
    cp rust/target/release/libbattle_engine_ffi.so storage/rust-libs/
    echo "Copied libbattle_engine_ffi.so"
else
    echo "ERROR: libbattle_engine_ffi.so not found after compilation!"
    exit 1
fi

# Compile the remaining workspace members (test_ffi, battle_engine_debug).
echo "Compiling Rust workspace..."
if ! cargo build "--manifest-path=rust/Cargo.toml" "--release"; then
    echo "ERROR: Rust compilation failed!"
    exit 1
fi

if [ -f rust/target/release/libtest_ffi.so ]; then
    cp rust/target/release/libtest_ffi.so storage/rust-libs/
    echo "Copied libtest_ffi.so"
fi

echo "Rust compilation completed successfully!"
