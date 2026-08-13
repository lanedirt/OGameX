#!/bin/sh

# Determine the shared library extension for the current platform.
# PHP loads these libraries through FFI, which accepts any extension, but
# Rust uses a platform specific one by default.
case "$(uname -s)" in
    Darwin)
        LIB_EXT="dylib"
        LIB_PREFIX="lib"
        ;;
    MINGW*|MSYS*|CYGWIN*)
        LIB_EXT="dll"
        LIB_PREFIX=""
        ;;
    *)
        LIB_EXT="so"
        LIB_PREFIX="lib"
        ;;
esac

BATTLE_ENGINE_LIB="${LIB_PREFIX}battle_engine_ffi.${LIB_EXT}"
TEST_FFI_LIB="${LIB_PREFIX}test_ffi.${LIB_EXT}"

# Compile the rust workspace
echo "Compiling Rust workspace..."
if ! cargo build "--manifest-path=rust/Cargo.toml" "--release"; then
    echo "ERROR: Rust compilation failed!"
    exit 1
fi

# Copy the compiled rust libraries to the storage/rust-libs directory.
# These shared libraries are called by Laravel.
if [ -f "rust/target/release/${BATTLE_ENGINE_LIB}" ]; then
    cp "rust/target/release/${BATTLE_ENGINE_LIB}" storage/rust-libs/
    echo "Copied ${BATTLE_ENGINE_LIB}"
else
    echo "ERROR: ${BATTLE_ENGINE_LIB} not found after compilation!"
    exit 1
fi

if [ -f "rust/target/release/${TEST_FFI_LIB}" ]; then
    cp "rust/target/release/${TEST_FFI_LIB}" storage/rust-libs/
    echo "Copied ${TEST_FFI_LIB}"
fi

echo "Rust compilation completed successfully!"
