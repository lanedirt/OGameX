<?php

namespace Tests\Feature;

use Exception;
use Illuminate\Testing\TestResponse;
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
     * @throws Exception
     */
    public function testResearchQueueCancelMultiple(): void
    {
        $this->planetAddResources(new Resources(0, 10000, 5000, 0));
        $this->planetSetObjectLevel('research_lab', 1);

        // ---
        // Step 1: Issue a request to build three levels of energy technology
        // ---
        for ($i = 0; $i <= 3; $i++) {
            $this->addResearchBuildRequest('energy_technology');
        }

        // Access the build queue page to verify the buildings are in the queue
        $this->travel(1)->seconds();

        $response = $this->get('/research');
        $this->assertObjectInQueue($response, 'energy_technology', 3, 'Energy Technology level 3 is expected in build queue but cannot be found.');

        $this->pressCancelButtonOnPage($response);

        // Verify that all buildings in the queue are now canceled
        $response = $this->get('/research');
        $response->assertStatus(200);
        $this->assertObjectNotInQueue($response, 'energy_technology', 'Energy Technology is in build queue but should have been canceled.');

        // Advance time by 30 minutes
        $this->travel(30)->minutes();

        // Verify that Energy Technology is still at level 0
        $response = $this->get('/research');
        $response->assertStatus(200);

        $this->assertObjectLevelOnPage($response, 'energy_technology', 0);
    }

    /**
     * Verify that when canceling a building in the build queue, the resources are refunded.
     * @throws Exception
     */
    public function testResearchQueueCancelRefundResources(): void
    {
        $this->planetAddResources(new Resources(0, 800, 400, 0));
        $this->planetSetObjectLevel('research_lab', 1);

        // Verify that we begin the test with 500 metal and 500 crystal
        $response = $this->get('/research');
        $response->assertStatus(200);
        $this->assertResourcesOnPage($response, new Resources(500, 1300, 400, 0));

        $this->addResearchBuildRequest('energy_technology');

        $response = $this->get('/research');
        $response->assertStatus(200);
        $this->assertObjectInQueue($response, 'energy_technology', 1, 'Energy Technology level 1 is not in build queue.');

        $this->pressCancelButtonOnPage($response);

        $response = $this->get('/research');
        $response->assertStatus(200);

        // Assert that the resources have been refunded and are again at 1300 crystal and 400 deuterium
        $this->assertResourcesOnPage($response, new Resources(500, 1300, 400, 0));
    }

    /**
     * Verify that canceling a second entry in the build queue works.
     * @throws Exception
     */
    public function testBuildQueueCancelSecondEntry(): void
    {
        $this->planetAddResources(new Resources(0, 1600, 1600, 0));
        $this->planetSetObjectLevel('research_lab', 1);

        $response = $this->get('/research');
        $response->assertStatus(200);

        // Build one level of energy technology
        $this->addResearchBuildRequest('energy_technology');
        // Then build one level of computer technology
        $this->addResearchBuildRequest('computer_technology');

        $response = $this->get('/research');
        $response->assertStatus(200);

        $this->assertObjectInQueue($response, 'computer_technology', 1, 'Computer Technology level 1 is not in build queue.');

        // Extract the content from the response
        $pageContent = $response->getContent();
        if (!$pageContent) {
            $pageContent = '';
        }
        // Use a regular expression to find all matches of 'onclick="cancelbuilding(num1,num2,'
        preg_match_all('/onclick="cancelbuilding\(\s*(\d+)\s*,\s*(\d+)\s*,/', $pageContent, $matches);

        // Check if there are at least three matches
        // First active build queue has two cancelProduction buttons.
        // The second active build queue has one cancelProduction button which will be the third.
        if (count($matches[0]) >= 3) {
            // Access the numbers from the second occurrence
            $number1 = (int)$matches[1][2];  // Second occurrence, first number
            $number2 = (int)$matches[2][2];  // Second occurrence, second number

            // Do POST to cancel build queue item:
            $this->cancelResearchBuildRequest($number1, $number2);

            // Assert that cancel build queue for computer technology is no longer visible
            $response = $this->get('/research');
            $response->assertStatus(200);
            $this->assertObjectNotInQueue($response, 'computer_technology', 'Computer Technology is in build queue but should have been canceled.');
        } else {
            $this->fail('Less than two "cancelbuilding" calls found.');
        }
    }

    /**
     * Verify that canceling a research queue item originally started on a different planet works.
     * @throws Exception
     */
    public function testBuildQueueCancelDifferentPlanet(): void
    {
        $this->planetAddResources(new Resources(0, 1600, 1600, 0));
        $this->planetSetObjectLevel('research_lab', 1);

        $response = $this->get('/research');
        $response->assertStatus(200);

        // Build one level of energy technology
        $this->addResearchBuildRequest('energy_technology');

        // Switch to a different planet
        $this->switchToSecondPlanet();

        $response = $this->get('/research');
        $response->assertStatus(200);

        $this->pressCancelButtonOnPage($response);

        $response = $this->get('/research');
        $response->assertStatus(200);
    }

    /**
     * Find the interactive cancel button on the page and then simulate a click on it by sending a POST request
     * with the correct parameters.
     *
     * @param TestResponse $response
     * @return void
     */
    private function pressCancelButtonOnPage(TestResponse $response): void
    {
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
        $number1 = (int)$cancelProductionCall[0];
        $number2 = (int)$cancelProductionCall[1];

        // Check if both numbers are integers. If not, throw an exception.
        if (empty($number1) || empty($number2)) {
            $this->fail('Could not extract the research queue ID from the page.');
        }

        // Do POST to cancel build queue item:
        $this->cancelResearchBuildRequest($number1, $number2);
    }

    /**
     * Tests research queue item is cancelled if requirements are not met.
     */
    public function testCancelResearchMissingRequirements(): void
    {
        // Assert that research queue is empty
        $response = $this->get('/research');
        $response->assertStatus(200);

        $this->assertEmptyResearchQueue($response);

        // Set facilities and add resources to planet that test requires.
        $this->planetSetObjectLevel('research_lab', 2);
        $this->planetAddResources(new Resources(5000, 5000, 5000, 0));

        $this->addResearchBuildRequest('energy_technology');
        $this->addResearchBuildRequest('impulse_drive');

        $response = $this->get('/research');
        $this->assertObjectInQueue($response, 'impulse_drive', 1, 'Impulse Drive 1 is not in research queue.');

        // Extract the first and second number from the first cancelbuilding call
        $cancelProductionCall = $response->getContent();
        if (empty($cancelProductionCall)) {
            $cancelProductionCall = '';
        }
        $cancelProductionCall = explode('onclick="cancelbuilding(', $cancelProductionCall);
        $cancelProductionCall = explode(',', $cancelProductionCall[1]);
        $number1 = (int)$cancelProductionCall[0];
        $number2 = (int)$cancelProductionCall[1];

        // Cancel Energy technology level 1, this will cancel also Impulse Drive level 1
        $this->cancelResearchBuildRequest($number1, $number2);

        // Assert that building queue is empty
        $response = $this->get('/research');
        $response->assertStatus(200);

        $this->assertEmptyResearchQueue($response);
    }
}
