<?php

namespace OGame\Services;

/**
 * Resolves the file system path to compiled Rust shared libraries.
 *
 * Rust produces platform specific shared library file names:
 * - Linux:   lib<name>.so
 * - macOS:   lib<name>.dylib
 * - Windows: <name>.dll
 *
 * PHP's FFI loads whatever file it is pointed at, so this helper ensures the
 * correct file is referenced regardless of the operating system PHP runs on.
 */
class RustFfiLibrary
{
    /**
     * Get the full path to a compiled Rust library within storage/rust-libs.
     *
     * @param string $name Library name without "lib" prefix or extension (e.g. "battle_engine_ffi").
     * @return string
     */
    public static function path(string $name): string
    {
        return base_path('storage/rust-libs/' . self::filename($name));
    }

    /**
     * Build the platform specific file name for a Rust library.
     *
     * @param string $name
     * @return string
     */
    public static function filename(string $name): string
    {
        return match (PHP_OS_FAMILY) {
            'Windows' => $name . '.dll',
            'Darwin' => "lib{$name}.dylib",
            default => "lib{$name}.so",
        };
    }
}
