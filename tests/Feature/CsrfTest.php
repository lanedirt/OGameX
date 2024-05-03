<?php

namespace Tests\Feature;

use Tests\AccountTestCase;

/**
 * Test that certain actions require a CSRF token.
 */
class CsrfTest extends AccountTestCase
{
    /**
     * Verify that issuing fastbuild requests without a CSRF token fails.
     */
    public function testFastBuildQueuesWithoutCsrfFail(): void
    {
        // Resources: Missing: _token=' . csrf_token() . '
        $response = $this->get('/resources/add-buildrequest?&type=1&planet_id=' . $this->planetService->getPlanetId());
        $response->assertStatus(500);

        // Facilities: Missing: _token=' . csrf_token() . '
        $response = $this->get('/facilities/add-buildrequest?&type=14&planet_id=' . $this->planetService->getPlanetId());
        $response->assertStatus(500);

        // Research: Missing: _token=' . csrf_token() . '
        $response = $this->get('/research/add-buildrequest?&type=113&planet_id=' . $this->planetService->getPlanetId());
        $response->assertStatus(500);
    }
}
