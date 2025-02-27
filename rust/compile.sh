#!/bin/sh

# Compile the rust workspace
cargo build "--manifest-path=rust/Cargo.toml" "--release --target x86_64-unknown-linux-musl"

# Copy the compiled rust libraries to the storage/rust-libs directory.
# The .so files are called by Laravel.
cp rust/target/release/lib*_ffi.so storage/rust-libs
