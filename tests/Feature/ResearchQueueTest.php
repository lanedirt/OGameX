<?php

namespace Feature;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use OGame\Models\Resources;
use Tests\AccountTestCase;
use Tests\TestCase;

/**
 * Test that the research queue works as expected.
 */
class ResearchQueueTest extends AccountTestCase
{
    /**
     * Verify that researching energy technology works as expected.
     * @throws BindingResolutionException
     */
    public function testResearchQueueEnergyTechnology(): void
    {
        // Add resources to planet that test requires.
        $this->planetAddResources(new Resources(0,800,400,0));
        // Set the research lab to level 1.
        $this->planetSetObjectLevel('research_lab', 1);

        // Set the current time to a specific moment for testing
        $testTime = Carbon::create(2024, 1, 1, 12, 0, 0);
        Carbon::setTestNow($testTime);

        // ---
        // Step 1: Issue a request to rsearch energy technology
        // ---
        $response = $this->post('/research/add-buildrequest', [
            '_token' => csrf_token(),
            'type' => '113', // Energy technology
            'planet_id' => $this->currentPlanetId,
        ]);

        // Assert the response status is successful (302 redirect).
        $response->assertStatus(302);

        // ---
        // Step 2: Verify the technology is in the research queue
        // ---
        // Check if the research is in the queue and is still level 0.
        $response = $this->get('/research');
        $response->assertStatus(200);
        $pattern = '/<span\s+class="level">\s*<span\s+class="textlabel">\s*Energy\sTechnology\s*<\/span>\s*0\s*<\/span>/';
        $result = preg_match($pattern, $response->getContent());
        $this->assertTrue($result === 1, 'Energy technology is not still at level 0 two seconds after build request issued.');

        // ---
        // Step 3: Verify the research is still in the build queue 1 minute later.
        // ---
        $testTime = Carbon::create(2024, 1, 1, 12, 1, 0);
        Carbon::setTestNow($testTime);

        // Check if the technology is still in the queue and is still level 0.
        $response = $this->get('/research');
        $response->assertStatus(200);
        $pattern = '/<span\s+class="level">\s*<span\s+class="textlabel">\s*Energy\sTechnology\s*<\/span>\s*0\s*<\/span>/';
        $result = preg_match($pattern, $response->getContent());
        $this->assertTrue($result === 1, 'Energy technology is not still at level 0 two seconds after build request issued.');

        // ---
        // Step 4: Verify the research is finished 10 minute later.
        // ---
        $testTime = Carbon::create(2024, 1, 1, 12, 10, 0);
        Carbon::setTestNow($testTime);

        // Check if the technology research is finished and is now level 1.
        $response = $this->get('/research');
        $response->assertStatus(200);
        $pattern = '/<span\s+class="level">\s*<span\s+class="textlabel">\s*Energy\sTechnology\s*<\/span>\s*1\s*<\/span>/';
        $result = preg_match($pattern, $response->getContent());
        $this->assertTrue($result === 1, 'Energy technology is not at level one 10 minutes after build request issued.');
    }

    /**
     * Verify that researching energy technology works as expected with fastbuild (GET).
     * @throws BindingResolutionException
     */
    public function testResearchQueueEnergyTechnologyFastBuild(): void
    {
        // Add resources to planet that test requires.
        $this->planetAddResources(new Resources(0,800,400,0));
        // Set the research lab to level 1.
        $this->planetSetObjectLevel('research_lab', 1);

        // Set the current time to a specific moment for testing
        $testTime = Carbon::create(2024, 1, 1, 12, 0, 0);
        Carbon::setTestNow($testTime);

        // ---
        // Step 1: Issue a request to rsearch energy technology
        // ---
        $response = $this->get('/research/add-buildrequest?_token=' . csrf_token() . '&type=113&planet_id=' . $this->currentPlanetId);

        // Assert the response status is successful (302 redirect).
        $response->assertStatus(302);

        // ---
        // Step 2: Verify the technology is in the research queue
        // ---
        // Check if the research is in the queue and is still level 0.
        $response = $this->get('/research');
        $response->assertStatus(200);
        $pattern = '/<span\s+class="level">\s*<span\s+class="textlabel">\s*Energy\sTechnology\s*<\/span>\s*0\s*<\/span>/';
        $result = preg_match($pattern, $response->getContent());
        $this->assertTrue($result === 1, 'Energy technology is not still at level 0 two seconds after build request issued.');

        // ---
        // Step 3: Verify the research is still in the build queue 1 minute later.
        // ---
        $testTime = Carbon::create(2024, 1, 1, 12, 1, 0);
        Carbon::setTestNow($testTime);

        // Check if the technology is still in the queue and is still level 0.
        $response = $this->get('/research');
        $response->assertStatus(200);
        $pattern = '/<span\s+class="level">\s*<span\s+class="textlabel">\s*Energy\sTechnology\s*<\/span>\s*0\s*<\/span>/';
        $result = preg_match($pattern, $response->getContent());
        $this->assertTrue($result === 1, 'Energy technology is not still at level 0 two seconds after build request issued.');

        // ---
        // Step 4: Verify the research is finished 10 minute later.
        // ---
        $testTime = Carbon::create(2024, 1, 1, 12, 10, 0);
        Carbon::setTestNow($testTime);

        // Check if the technology research is finished and is now level 1.
        $response = $this->get('/research');
        $response->assertStatus(200);
        $pattern = '/<span\s+class="level">\s*<span\s+class="textlabel">\s*Energy\sTechnology\s*<\/span>\s*1\s*<\/span>/';
        $result = preg_match($pattern, $response->getContent());
        $this->assertTrue($result === 1, 'Energy technology is not at level one 10 minutes after build request issued.');
    }

    /**
     * Verify that researching multiple technologies works as expected.
     * @throws BindingResolutionException
     */
    public function testResearchQueueMultiQueue(): void
    {
        // Add resources to planet that test requires.
        $this->planetAddResources(new Resources(0,2400,1200,0));
        // Set the research lab to level 1.
        $this->planetSetObjectLevel('research_lab', 1);

        // Set the current time to a specific moment for testing
        $testTime = Carbon::create(2024, 1, 1, 12, 0, 0);
        Carbon::setTestNow($testTime);

        // ---
        // Step 1: Issue a request to rsearch energy technology
        // ---
        $response = $this->post('/research/add-buildrequest', [
            '_token' => csrf_token(),
            'type' => '113', // Energy technology
            'planet_id' => $this->currentPlanetId,
        ]);
        $response->assertStatus(302);
        $response = $this->post('/research/add-buildrequest', [
            '_token' => csrf_token(),
            'type' => '113', // Energy technology
            'planet_id' => $this->currentPlanetId,
        ]);
        $response->assertStatus(302);

        // ---
        // Step 2: Verify the technology is in the research queue
        // ---
        // Check if the research is in the queue and is still level 0.
        $response = $this->get('/research');
        $response->assertStatus(200);
        $pattern = '/<span\s+class="level">\s*<span\s+class="textlabel">\s*Energy\sTechnology\s*<\/span>\s*0\s*<\/span>/';
        $result = preg_match($pattern, $response->getContent());
        $this->assertTrue($result === 1, 'Energy technology is not still at level 0 two seconds after build request issued.');

        // ---
        // Step 3: Verify that 1 research queue item is finished 15 minutes later.
        // ---
        $testTime = Carbon::create(2024, 1, 1, 12, 15, 0);
        Carbon::setTestNow($testTime);

        // Check if the research is finished and is now level 1.
        $response = $this->get('/research');
        $response->assertStatus(200);
        $pattern = '/<span\s+class="level">\s*<span\s+class="textlabel">\s*Energy\sTechnology\s*<\/span>\s*1\s*<\/span>/';
        $result = preg_match($pattern, $response->getContent());
        $this->assertTrue($result === 1, 'Energy technology is not at level one 15 minutes after build request issued.');

        // ---
        // Step 3: Verify that 1 research queue item is finished 330 minutes later.
        // ---
        $testTime = Carbon::create(2024, 1, 1, 12, 30, 0);
        Carbon::setTestNow($testTime);

        // Check if the research is finished and is now level 1.
        $response = $this->get('/research');
        $response->assertStatus(200);
        $pattern = '/<span\s+class="level">\s*<span\s+class="textlabel">\s*Energy\sTechnology\s*<\/span>\s*2\s*<\/span>/';
        $result = preg_match($pattern, $response->getContent());
        $this->assertTrue($result === 1, 'Energy technology is not at level two 30 minutes after build request issued.');
    }

    /**
     * Verify that when canceling a building in the build queue, the resources are refunded.
     * @throws BindingResolutionException
     */
    public function testResearchQueueCancelRefundResources(): void
    {
        // Add resources to planet that test requires.
        $this->planetAddResources(new Resources(0,800,400,0));
        // Set the research lab to level 1.
        $this->planetSetObjectLevel('research_lab', 1);

        // Set the current time to a specific moment for testing
        $testTime = Carbon::create(2024, 1, 1, 12, 0, 0);
        Carbon::setTestNow($testTime);

        // ---------

        // Verify that we begin the test with 500 metal and 500 crystal
        $response = $this->get('/research');
        $response->assertStatus(200);

        $pattern = '/<span\s+id="resources_crystal" class="">\s*1,300\s*<\/span>/';
        $result = preg_match($pattern, $response->getContent());
        $this->assertTrue($result === 1, 'Not starting test at 1300 crystal. Verify starting resources and update tests accordingly.');

        $pattern = '/<span\s+id="resources_deuterium" class="">\s*400\s*<\/span>/';
        $result = preg_match($pattern, $response->getContent());
        $this->assertTrue($result === 1, 'Not starting test at 400 deuterium. Verify starting resources and update tests accordingly.');

        $response = $this->post('/research/add-buildrequest', [
            '_token' => csrf_token(),
            'type' => '113', // Energy technology
            'planet_id' => $this->currentPlanetId,
        ]);
        // Assert the response status is successful (302 redirect).
        $response->assertStatus(302);

        // ---------
        $response = $this->get('/research');
        $response->assertStatus(200);

        // Assert that resources have been actually deducted
        $pattern = '/<span\s+id="resources_crystal" class="">\s*500\s*<\/span>/';
        $result = preg_match($pattern, $response->getContent());
        $this->assertTrue($result === 1, 'Resources have not been properly deducted when starting research.');

        $pattern = '/<span\s+id="resources_deuterium" class="">\s*0\s*<\/span>/';
        $result = preg_match($pattern, $response->getContent());
        $this->assertTrue($result === 1, 'Resources have not been properly deducted when starting research.');

        $response->assertSee('Cancel expansion of Energy Technology');
        // Extract first and second number on page which looks like this where num1/num2 are ints:
        // "cancelProduction(num1,num2,"
        $response->assertSee('cancelProduction(');

        // Extract the first and second number from the first cancelProduction call
        $cancelProductionCall = $response->getContent();
        $cancelProductionCall = explode('onclick="cancelProduction(', $cancelProductionCall);
        $cancelProductionCall = explode(',', $cancelProductionCall[1]);
        $number1 = $cancelProductionCall[0];
        $number2 = $cancelProductionCall[1];

        // Check if both numbers are integers. If not, throw an exception.
        if (!is_numeric($number1) || !is_numeric($number2)) {
            throw new BindingResolutionException('Could not extract the research queue ID from the page.');
        }

        // Do POST to cancel build queue item:
        $response = $this->post('/research/cancel-buildrequest', [
            '_token' => csrf_token(),
            'building_id' => $number1,
            'building_queue_id' => $number2,
            'planet_id' => $this->currentPlanetId,
        ]);

        // ---------

        // Assert the response status is successful (302 redirect).
        $response->assertStatus(302);
        $response = $this->get('/research');
        $response->assertStatus(200);

        // Assert that resources have been refunded.
        $pattern = '/<span\s+id="resources_crystal" class="">\s*1,300\s*<\/span>/';
        $result = preg_match($pattern, $response->getContent());
        $this->assertTrue($result === 1, 'Resources have not been refunded after research build cancellation.');

        $pattern = '/<span\s+id="resources_deuterium" class="">\s*400\s*<\/span>/';
        $result = preg_match($pattern, $response->getContent());
        $this->assertTrue($result === 1, 'Resources have not been refunded after research build cancellation.');
    }

    /**
     * Verify that researching energy technology fails if the planet is not owned by the player.
     * @throws BindingResolutionException
     */
    public function testResearchQueueNonExistentPlanet(): void
    {
        // Add resources to planet that test requires.
        $this->planetAddResources(new Resources(0,800,400,0));
        // Set the research lab to level 1.
        $this->planetSetObjectLevel('research_lab', 1);

        // ---
        // Step 1: Issue a request to research energy technology
        // ---
        $response = $this->post('/research/add-buildrequest', [
            '_token' => csrf_token(),
            'type' => '113', // Energy technology
            'planet_id' => $this->currentPlanetId - 1,
        ]);

        // Assert the response status has failed (500).
        $response->assertStatus(500);
    }
}
