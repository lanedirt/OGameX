<?php

namespace Tests;

use Illuminate\Support\Str;

/**
 * Base class for tests that require database context without the need for a new account context.
 */
abstract class DatabaseTestCase extends TestCase
{
    /**
     * Set up common test components.
     */
    protected function setUp(): void
    {
        parent::setUp();
    }
}
