<?php

namespace Tests\Feature;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Carbon;
use OGame\Models\Resources;
use Tests\AccountTestCase;

/**
 * Test AJAX calls to make sure they work as expected.
 */
class ResearchQueueCancelTest extends AccountTestCase
{
    /**
     * Verify that when adding more than one of the same technology to the research queue, that cancellation
     * of the first research in the queue will cancel all research of that type in the queue.
     * @throws BindingResolutionException
     */
    public function testResearchQueueCancelMultiple(): void
    {
        // Add resources to planet that test requires.
        $this->planetAddResources(new Resources(0,10000,5000,0));
        // Set the research lab to level 1.
        $this->planetSetObjectLevel('research_lab', 1);

        // Set the current time to a specific moment for testing
        $testTime = Carbon::create(2024, 1, 1, 12, 0, 0);
        Carbon::setTestNow($testTime);

        // ---
        // Step 1: Issue a request to build three levels of energy technology
        // ---
        for ($i = 0; $i <= 3; $i++) {
            $response = $this->post('/research/add-buildrequest', [
                '_token' => csrf_token(),
                'type' => '113', // Energy Technology
                'planet_id' => $this->currentPlanetId,
            ]);
            // Assert the response status is successful (302 redirect).
            $response->assertStatus(302);
        }

        // Access the build queue page to verify the buildings are in the queue
        $testTime = Carbon::create(2024, 1, 1, 12, 0, 1);
        Carbon::setTestNow($testTime);

        $response = $this->get('/research');
        $response->assertStatus(200);
        $response->assertSee('Cancel expansion of Energy Technology');

        // Extract first and second number on page which looks like this where num1/num2 are ints:
        // "cancelProduction(num1,num2,"
        $response->assertSee('cancelProduction(');

        // Extract the first and second number from the first cancelProduction call
        $cancelProductionCall = $response->getContent();
        if (empty($cancelProductionCall)) {
            $cancelProductionCall = '';
        }
        $cancelProductionCall = explode('onclick="cancelProduction(', $cancelProductionCall);
        $cancelProductionCall = explode(',', $cancelProductionCall[1]);
        $number1 = $cancelProductionCall[0];
        $number2 = $cancelProductionCall[1];

        // Check if both numbers are integers. If not, throw an exception.
        if (!is_numeric($number1) || !is_numeric($number2)) {
            throw new BindingResolutionException('Could not extract the building queue ID from the page.');
        }

        // Do POST to cancel build queue item:
        $response = $this->post('/research/cancel-buildrequest', [
            '_token' => csrf_token(),
            'building_id' => $number1,
            'building_queue_id' => $number2,
            'planet_id' => $this->currentPlanetId,
        ]);

        // Assert the response status is successful (302 redirect).
        $response->assertStatus(302);

        // Verify that all buildings in the queue are now canceled
        $response = $this->get('/research');
        $response->assertStatus(200);
        $response->assertDontSee('Cancel expansion of Energy Technology');

        // Advance time by 30 minutes
        $testTime = Carbon::create(2024, 1, 1, 12, 30, 0);
        Carbon::setTestNow($testTime);

        // Verify that Energy Technology is still at level 0
        $response = $this->get('/research');
        $response->assertStatus(200);

        $this->assertObjectLevelOnPage($response, 'energy_technology', 0);
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

        // Verify that we begin the test with 500 metal and 500 crystal
        $response = $this->get('/research');
        $response->assertStatus(200);
        $this->assertResourcesOnPage($response, new Resources(500, 1300, 400, 0));

        $response = $this->post('/research/add-buildrequest', [
            '_token' => csrf_token(),
            'type' => '113', // Energy Technology
            'planet_id' => $this->currentPlanetId,
        ]);
        // Assert the response status is successful (302 redirect).
        $response->assertStatus(302);

        $response = $this->get('/research');
        $response->assertStatus(200);
        $response->assertSee('Cancel expansion of Energy Technology');

        // Extract first and second number on page which looks like this where num1/num2 are ints:
        // "cancelProduction(num1,num2,"
        $response->assertSee('cancelProduction(');

        // Extract the first and second number from the first cancelProduction call
        $cancelProductionCall = $response->getContent();
        if (empty($cancelProductionCall)) {
            $cancelProductionCall = '';
        }
        $cancelProductionCall = explode('onclick="cancelProduction(', $cancelProductionCall);
        $cancelProductionCall = explode(',', $cancelProductionCall[1]);
        $number1 = $cancelProductionCall[0];
        $number2 = $cancelProductionCall[1];

        // Check if both numbers are integers. If not, throw an exception.
        if (!is_numeric($number1) || !is_numeric($number2)) {
            throw new BindingResolutionException('Could not extract the technology queue ID from the page.');
        }

        // Do POST to cancel build queue item:
        $response = $this->post('/research/cancel-buildrequest', [
            '_token' => csrf_token(),
            'building_id' => $number1,
            'building_queue_id' => $number2,
            'planet_id' => $this->currentPlanetId,
        ]);

        // Assert the response status is successful (302 redirect).
        $response->assertStatus(302);
        $response = $this->get('/research');
        $response->assertStatus(200);

        // Assert that the resources have been refunded and are again at 1300 crystal and 400 deuterium
        $this->assertResourcesOnPage($response, new Resources(500, 1300, 400, 0));
    }

    /**
     * Verify that canceling a second entry in the build queue works.
     * @throws BindingResolutionException
     */
    public function testBuildQueueCancelSecondEntry(): void
    {
        // Add resources to planet that test requires.
        $this->planetAddResources(new Resources(0,1600,1600,0));
        // Set the research lab to level 1.
        $this->planetSetObjectLevel('research_lab', 1);

        // Set the current time to a specific moment for testing
        $testTime = Carbon::create(2024, 1, 1, 12, 0, 0);
        Carbon::setTestNow($testTime);

        $response = $this->get('/research');
        $response->assertStatus(200);

        // Build one level of energy technology
        $response = $this->post('/research/add-buildrequest', [
            '_token' => csrf_token(),
            'type' => '113', // Energy Technology
            'planet_id' => $this->currentPlanetId,
        ]);
        $response->assertStatus(302);
        // Then build one level of computer technology
        $response = $this->post('/research/add-buildrequest', [
            '_token' => csrf_token(),
            'type' => '108', // Computer Technology
            'planet_id' => $this->currentPlanetId,
        ]);
        $response->assertStatus(302);

        $response = $this->get('/research');
        $response->assertStatus(200);

        // Extract first and second number on page which looks like this where num1/num2 are ints:
        // "cancelProduction(num1,num2,"
        $response->assertSee('Cancel expansion of Computer Technology to level 1?');

        // Extract the content from the response
        $pageContent = $response->getContent();
        if (empty($pageContent)) {
            $pageContent = '';
        }
        // Use a regular expression to find all matches of 'onclick="cancelProduction(num1,num2,'
        preg_match_all('/onclick="cancelProduction\((\d+),(\d+),/', $pageContent, $matches);

        // Check if there are at least three matches
        // First active build queue has two cancelProduction buttons.
        // The second active build queue has one cancelProduction button which will be the third.
        if (count($matches[0]) >= 3) {
            // Access the numbers from the second occurrence
            $number1 = $matches[1][2];  // Second occurrence, first number
            $number2 = $matches[2][2];  // Second occurrence, second number

            // Do POST to cancel build queue item:
            $response = $this->post('/research/cancel-buildrequest', [
                '_token' => csrf_token(),
                'building_id' => $number1,
                'building_queue_id' => $number2,
                'planet_id' => $this->currentPlanetId,
            ]);

            // Assert the response status is successful (302 redirect).
            $response->assertStatus(302);

            // Assert that cancel build queue for computer technology is no longer visible
            $response = $this->get('/research');
            $response->assertStatus(200);
            $response->assertDontSee('Cancel expansion of Computer Technology to level 1?');
        } else {
            $this->throwException(new BindingResolutionException('Less than two "cancelProduction" calls found.'));
        }
    }
}
