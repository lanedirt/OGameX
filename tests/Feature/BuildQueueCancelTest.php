<?php

namespace Tests\Feature;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Carbon;
use OGame\Models\Resources;
use Tests\AccountTestCase;

/**
 * Test AJAX calls to make sure they work as expected.
 */
class BuildQueueCancelTest extends AccountTestCase
{
    /**
     * Verify that when adding more than one of the same building to the build queue, that cancellation
     * of the first building in the queue will cancel all buildings of that type in the queue.
     * @throws BindingResolutionException
     */
    public function testBuildQueueCancelMultiple(): void
    {
        // Set the current time to a specific moment for testing
        $testTime = Carbon::create(2024, 1, 1, 12, 0, 0);
        Carbon::setTestNow($testTime);

        // ---
        // Step 1: Issue a request to build three metal mines
        // ---
        for ($i = 0; $i <= 3; $i++) {
            $response = $this->post('/resources/add-buildrequest', [
                '_token' => csrf_token(),
                'type' => '1', // Metal mine
                'planet_id' => $this->currentPlanetId,
            ]);
            // Assert the response status is successful (302 redirect).
            $response->assertStatus(302);
        }

        // Access the build queue page to verify the buildings are in the queue
        $testTime = Carbon::create(2024, 1, 1, 12, 0, 1);
        Carbon::setTestNow($testTime);

        $response = $this->get('/resources');
        $response->assertStatus(200);
        $response->assertSee('Cancel expansion of Metal Mine');

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
        $response = $this->post('/resources/cancel-buildrequest', [
            '_token' => csrf_token(),
            'building_id' => $number1,
            'building_queue_id' => $number2,
            'planet_id' => $this->currentPlanetId,
        ]);

        // Assert the response status is successful (302 redirect).
        $response->assertStatus(302);

        // Verify that all buildings in the queue are now canceled
        $response = $this->get('/resources');
        $response->assertStatus(200);
        $response->assertDontSee('Cancel expansion of Metal Mine');

        // Advance time by 30 minutes
        $testTime = Carbon::create(2024, 1, 1, 12, 30, 0);
        Carbon::setTestNow($testTime);

        // Verify that Metal Mine is still at level 0
        $response = $this->get('/resources');
        $content = $response->getContent();
        if (empty($content)) {
            $content = '';
        }
        $response->assertStatus(200);
        $pattern = '/<span\s+class="level">\s*<span\s+class="textlabel">\s*Metal\sMine\s*<\/span>\s*0\s*<\/span>/';
        $result = preg_match($pattern, $content);
        $this->assertTrue($result === 1, 'Metal Mine has been built while all jobs should have been canceled.');
    }

    /**
     * Verify that when canceling a building in the build queue, the resources are refunded.
     * @throws BindingResolutionException
     */
    public function testBuildQueueCancelRefundResources(): void
    {
        // Set the current time to a specific moment for testing
        $testTime = Carbon::create(2024, 1, 1, 12, 0, 0);
        Carbon::setTestNow($testTime);

        // Verify that we begin the test with 500 metal and 500 crystal
        $response = $this->get('/resources');
        $response->assertStatus(200);

        $this->assertResourcesOnPage($response, new Resources(500, 500, 0, 0));

        $response = $this->post('/resources/add-buildrequest', [
            '_token' => csrf_token(),
            'type' => '1', // Metal mine
            'planet_id' => $this->currentPlanetId,
        ]);
        // Assert the response status is successful (302 redirect).
        $response->assertStatus(302);

        $response = $this->get('/resources');
        $response->assertStatus(200);
        $response->assertSee('Cancel expansion of Metal Mine');

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
        $response = $this->post('/resources/cancel-buildrequest', [
            '_token' => csrf_token(),
            'building_id' => $number1,
            'building_queue_id' => $number2,
            'planet_id' => $this->currentPlanetId,
        ]);

        // Assert the response status is successful (302 redirect).
        $response->assertStatus(302);
        $response = $this->get('/resources');
        $response->assertStatus(200);

        // Assert that the resources have been refunded and are again at 500 metal and 500 crystal
        $this->assertResourcesOnPage($response, new Resources(500, 500, 0, 0));
    }

    /**
     * Verify that canceling a second entry in the build queue works.
     */
    public function testBuildQueueCancelSecondEntry(): void
    {
        // Set the current time to a specific moment for testing
        $testTime = Carbon::create(2024, 1, 1, 12, 0, 0);
        Carbon::setTestNow($testTime);

        // Verify that we begin the test with 500 metal and 500 crystal
        $response = $this->get('/resources');
        $response->assertStatus(200);

        // Build one level of metal mine
        $response = $this->post('/resources/add-buildrequest', [
            '_token' => csrf_token(),
            'type' => '1', // Metal mine
            'planet_id' => $this->currentPlanetId,
        ]);
        $response->assertStatus(302);
        // Then build one level of crystal mine
        $response = $this->post('/resources/add-buildrequest', [
            '_token' => csrf_token(),
            'type' => '2', // Crystal mine
            'planet_id' => $this->currentPlanetId,
        ]);
        $response->assertStatus(302);

        $response = $this->get('/resources');
        $response->assertStatus(200);

        // Extract first and second number on page which looks like this where num1/num2 are ints:
        // "cancelProduction(num1,num2,"
        $response->assertSee('Cancel expansion of Crystal Mine to level 1?');

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
            $response = $this->post('/resources/cancel-buildrequest', [
                '_token' => csrf_token(),
                'building_id' => $number1,
                'building_queue_id' => $number2,
                'planet_id' => $this->currentPlanetId,
            ]);

            // Assert the response status is successful (302 redirect).
            $response->assertStatus(302);

            // Assert that cancel build queue for crystal mine is no longer visible
            $response = $this->get('/resources');
            $response->assertStatus(200);
            $response->assertDontSee('Cancel expansion of Crystal Mine to level 1?');
        } else {
            $this->throwException(new BindingResolutionException('Less than two "cancelProduction" calls found.'));
        }
    }
}
