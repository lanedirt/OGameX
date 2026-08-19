<?php

namespace Tests\Unit;

use FFI;
use OGame\Services\RustFfiLibrary;
use Tests\UnitTestCase;

class RustFfiTest extends UnitTestCase
{
    /**
     * Test that we can call a Rust test program via PHP FFI.
     */
    public function testRustFfiInterface(): void
    {
        // Resolve the platform-specific shared library path.
        $libPath = RustFfiLibrary::path('test_ffi');

        // Define the function signature in C syntax
        $ffi = FFI::cdef("
            char* rust_hello(void);
        ", $libPath);

        // Call the Rust function and get the returned string
        /** @phpstan-ignore-next-line */
        $result = $ffi->rust_hello();

        // Convert the C string to a PHP string
        $output = FFI::string($result);

        // Assert the expected output
        $this->assertEquals("Hello from Rust!", $output);
    }
}
