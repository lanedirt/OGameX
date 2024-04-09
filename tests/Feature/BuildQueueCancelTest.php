<?php

namespace Tests\Feature;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Carbon;
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
        $response->assertSee('cancelProduction(');

        // Extract the first and second number from the first cancelProduction call
        $cancelProductionCall = $response->getContent();
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
        $response->assertStatus(200);
        $pattern = '/<span\s+class="level">\s*<span\s+class="textlabel">\s*Metal\sMine\s*<\/span>\s*0\s*<\/span>/';
        $result = preg_match($pattern, $response->getContent());
        $this->assertTrue($result === 1, 'Metal Mine has been built while all jobs should have been canceled.');
    }
}
