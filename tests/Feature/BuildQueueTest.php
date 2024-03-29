<?php

namespace Feature;

use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use OGame\Services\ObjectService;
use Tests\TestCase;

/**
 * Test AJAX calls to make sure they work as expected.
 */
class BuildQueueTest extends TestCase
{
    protected $currentUserId = 0;
    protected $currentPlanetId = 0;

    /**
     * Set up common test components.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Create a new user and login so we can access ingame features
        $this->createAndLoginUser();
    }

    protected function createAndLoginUser() {
        $response = $this->get('/login');

        // Check for existence of register form
        $response->assertSee('subscribeForm');

        // Simulate form data
        // Generate random email
        $randomEmail = Str::random(10) . '@example.com';

        $formData = [
            '_token' => csrf_token(),
            'email' => $randomEmail,
            'password' => 'asdasdasd',
            'v' => '3',
            'step' => 'validate',
            'kid' => '',
            'errorCodeOn' => '1',
            'is_utf8' => '1',
            'agb' => 'on',
        ];

        // Submit the registration form
        $this->post('/register', $formData);

        // We should now automatically be logged in. Retrieve meta fields to verify.
        $this->retrieveMetaFields();
    }

    protected function retrieveMetaFields() {
        //  Extract current user planet ID based on meta tag in the overview page
        $response = $this->get('/overview');

        $content = $response->getContent();

        preg_match('/<meta name="ogame-planet-id" content="([^"]+)"/', $content, $planetIdMatches);
        preg_match('/<meta name="ogame-player-id" content="([^"]+)"/', $content, $playerIdMatches);

        $playerId = $playerIdMatches[1] ?? null;
        $planetId = $planetIdMatches[1] ?? null;

        // Now you can assert these values to ensure they are what you expect.
        $this->assertNotEmpty($playerId);
        $this->assertNotEmpty($planetId);

        $this->currentUserId = $playerId;
        $this->currentPlanetId = $planetId;
    }

    /**
     * Verify that building a metal mine works as expected.
     */
    public function testBuildQueueMetalMine(): void
    {
        // Set the current time to a specific moment for testing
        $testTime = Carbon::create(2024, 1, 1, 12, 0, 0);
        Carbon::setTestNow($testTime);

        // ---
        // Step 1: Issue a request to build a metal mine
        // ---
        $response = $this->post('/resources/add-buildrequest', [
            'token' => csrf_token(),
            'type' => '1',
            'planet_id' => $this->currentPlanetId,
        ]);

        // Assert the response status is successful (302 redirect).
        $response->assertStatus(302);

        // ---
        // Step 2: Verify the building is in the build queue
        // ---
        // Check if the building is in the queue and is still level 0.
        $response = $this->get('/resources');
        $pattern = '/<span\s+class="level">\s*<span\s+class="textlabel">\s*Metal\s+Mine\s*<\/span>\s*0\s*<\/span>/';
        $result = preg_match($pattern, $response->getContent());
        $this->assertTrue($result === 1, 'Metal mine is not still at level 0 two seconds after build request issued.');

        // ---
        // Step 3: Verify the building is still in the build queue 2 seconds later.
        // ---
        $testTime = Carbon::create(2024, 1, 1, 12, 0, 2);
        Carbon::setTestNow($testTime);

        // Check if the building is still in the queue and is still level 0.
        $response = $this->get('/resources');
        $pattern = '/<span\s+class="level">\s*<span\s+class="textlabel">\s*Metal\s+Mine\s*<\/span>\s*0\s*<\/span>/';
        $result = preg_match($pattern, $response->getContent());
        $this->assertTrue($result === 1, 'Metal mine is not still at level 0 two seconds after build request issued.');

        // ---
        // Step 4: Verify the building is finished 1 minute later.
        // ---
        $testTime = Carbon::create(2024, 1, 1, 12, 1, 0);
        Carbon::setTestNow($testTime);

        // Check if the building is finished and is now level 1.
        $response = $this->get('/resources');
        $pattern = '/<span\s+class="level">\s*<span\s+class="textlabel">\s*Metal\s+Mine\s*<\/span>\s*1\s*<\/span>/';
        $result = preg_match($pattern, $response->getContent());
        $this->assertTrue($result === 1, 'Metal mine is not at level 1 one minute after build request issued.');
    }
}
