<?php

namespace Tests\Feature;

use Illuminate\Contracts\Container\BindingResolutionException;
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
        // ---
        // Step 1: Issue a request to build three metal mines
        // ---
        for ($i = 0; $i <= 3; $i++) {
            $this->addResourceBuildRequest('metal_mine');
        }

        // Access the build queue page to verify the buildings are in the queue
        $this->travel(1)->seconds();

        $response = $this->get('/resources');
        $this->assertObjectInQueue($response, 'metal_mine', 3, 'Metal mine level 3 is expected in build queue but cannot be found.');

        // Extract first and second number on page which looks like this where num1/num2 are ints:
        // "cancelProduction(num1,num2,"
        $response->assertSee('cancelbuilding(');

        // Extract the first and second number from the first cancelProduction call
        $cancelProductionCall = $response->getContent();
        if (empty($cancelProductionCall)) {
            $cancelProductionCall = '';
        }
        $cancelProductionCall = explode('onclick="cancelbuilding(', $cancelProductionCall);
        $cancelProductionCall = explode(',', $cancelProductionCall[1]);
        $number1 = (int)trim($cancelProductionCall[0]);
        $number2 = (int)trim($cancelProductionCall[1]);

        // Check if both numbers are integers. If not, throw an exception.
        if (empty($number1) || empty($number2)) {
            throw new BindingResolutionException('Could not extract the building queue ID from the page.');
        }

        // Cancel build queue item:
        $this->cancelResourceBuildRequest($number1, $number2);

        // Assert the response status is successful
        $response->assertStatus(200);

        // Verify that all buildings in the queue are now canceled
        $response = $this->get('/resources');
        $response->assertStatus(200);
        $this->assertObjectNotInQueue($response, 'metal_mine', 'Metal mine is in build queue but should have been canceled.');

        // Advance time by 30 minutes
        $this->travel(30)->minutes();

        // Verify that Metal Mine is still at level 0
        $response = $this->get('/resources');
        $this->assertObjectLevelOnPage($response, 'metal_mine', 0, 'Metal Mine has been built while all jobs should have been canceled.');
    }

    /**
     * Verify that when canceling a building in the build queue, the resources are refunded.
     * @throws BindingResolutionException
     * @throws \Exception
     */
    public function testBuildQueueCancelRefundResources(): void
    {
        // Verify that we begin the test with 500 metal and 500 crystal
        $response = $this->get('/resources');
        $response->assertStatus(200);

        $this->assertResourcesOnPage($response, new Resources(500, 500, 0, 0));
        $this->addResourceBuildRequest('metal_mine');

        $response = $this->get('/resources');
        $this->assertObjectInQueue($response, 'metal_mine', 1, 'Metal mine level 1 is not in build queue.');

        // Extract first and second number on page which looks like this where num1/num2 are ints:
        // "cancelProduction(num1,num2,"
        $response->assertSee('cancelbuilding(');

        // Extract the first and second number from the first cancelProduction call
        $cancelProductionCall = $response->getContent();
        if (empty($cancelProductionCall)) {
            $cancelProductionCall = '';
        }
        $cancelProductionCall = explode('onclick="cancelbuilding(', $cancelProductionCall);
        $cancelProductionCall = explode(',', $cancelProductionCall[1]);
        $number1 = (int)trim($cancelProductionCall[0]);
        $number2 = (int)trim($cancelProductionCall[1]);

        // Check if both numbers are integers. If not, throw an exception.
        if (empty($number1) || empty($number2)) {
            throw new BindingResolutionException('Could not extract the building queue ID from the page.');
        }

        // Cancel build queue item:
        $this->cancelResourceBuildRequest($number1, $number2);

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
        // Verify that we begin the test with 500 metal and 500 crystal
        $response = $this->get('/resources');
        $response->assertStatus(200);

        // Build one level of metal mine
        $this->addResourceBuildRequest('metal_mine');
        // Then build one level of crystal mine
        $this->addResourceBuildRequest('crystal_mine');

        $response = $this->get('/resources');
        $response->assertStatus(200);

        // Extract first and second number on page which looks like this where num1/num2 are ints:
        // "cancelbuilding(num1,num2,"
        $this->assertObjectInQueue($response, 'crystal_mine', 1, 'Crystal mine level 1 is not in build queue.');

        // Extract the content from the response
        $pageContent = $response->getContent();
        if (empty($pageContent)) {
            $pageContent = '';
        }

        // Use a regular expression to find all matches of 'onclick="cancelProduction(num1,num2,'
        preg_match_all('/onclick="cancelbuilding\((\d+),(\d+),/', $pageContent, $matches);

        // Check if there are at least three matches
        // First active build queue has two cancelbuilding buttons.
        // The second active build queue has one cancelbuilding button which will be the third.
        if (count($matches[0]) >= 3) {
            // Access the numbers from the second occurrence
            $number1 = (int)trim($matches[1][2]);  // Second occurrence, first number
            $number2 = (int)trim($matches[2][2]);  // Second occurrence, second number

            // Cancel build queue item:
            $this->cancelResourceBuildRequest($number1, $number2);

            // Assert that cancel build queue for crystal mine is no longer visible
            $response = $this->get('/resources');
            $this->assertObjectNotInQueue($response, 'crystal_mine', 'Crystal mine is not in build queue.');
        } else {
            $this->throwException(new BindingResolutionException('Less than two "cancelProduction" calls found.'));
        }
    }

    /**
     * Tests building queue item is cancelled if requirements are not met.
     */
    public function testCancelObjectMissingRequirements(): void
    {
        // Assert that build queue is empty
        $response = $this->get('/facilities');
        $response->assertStatus(200);

        $this->assertEmptyBuildingQueue($response);

        // Add resource to build required facilities to planet
        $this->planetAddResources(new Resources(5000, 5000, 5000, 0));

        // Add required facilities to building queue
        $this->addFacilitiesBuildRequest('robot_factory');
        $this->addFacilitiesBuildRequest('robot_factory');
        $this->addFacilitiesBuildRequest('shipyard');

        $response = $this->get('/facilities');
        $this->assertObjectInQueue($response, 'shipyard', 1, 'Shipyard level 1 is not in build queue.');

        // Extract the first and second number from the first cancelbuilding call
        $cancelProductionCall = $response->getContent();
        if (empty($cancelProductionCall)) {
            $cancelProductionCall = '';
        }
        $cancelProductionCall = explode('onclick="cancelbuilding(', $cancelProductionCall);
        $cancelProductionCall = explode(',', $cancelProductionCall[1]);
        $number1 = (int)$cancelProductionCall[0];
        $number2 = (int)$cancelProductionCall[1];

        // Check if both numbers are integers. If not, throw an exception.
        if (empty($number1) || empty($number2)) {
            throw new BindingResolutionException('Could not extract the building queue ID from the page.');
        }

        // Cancel Robotics Factory level 1, this will cancel also Robotics Factory level 2 and Shipyard level 1
        $this->cancelFacilitiesBuildRequest($number1, $number2);

        // Assert that building queue is empty
        $response = $this->get('/facilities');
        $response->assertStatus(200);

        $this->assertEmptyBuildingQueue($response);
    }
}
