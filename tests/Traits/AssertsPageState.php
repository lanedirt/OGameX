<?php

namespace Tests\Traits;

use Exception;
use Illuminate\Testing\TestResponse;
use OGame\Models\Resources;
use OGame\Services\ObjectService;

/**
 * Page-level assertions over the ingame HTML responses (object levels, resources,
 * build/research queue contents and requirement warnings).
 *
 * @method never fail(string $message = '')
 * @method void assertEquals(mixed $expected, mixed $actual, string $message = '')
 * @method void assertTrue(mixed $condition, string $message = '')
 */
trait AssertsPageState
{
    /**
     * Assert that the object level is as expected on the page.
     */
    protected function assertObjectLevelOnPage(TestResponse $response, string $machineName, int $expectedLevel, string $errorMessage = ''): void
    {
        $response->assertStatus(200);

        try {
            $object = ObjectService::getObjectByMachineName($machineName);
        } catch (Exception $e) {
            $this->fail('Failed to get object by machine name: ' . $machineName . '. Error: ' . $e->getMessage());
        }

        // Extract level from data-value attribute.
        $pattern = '/<li[^>]*\bclass="[^"]*\b' . preg_quote($object->class_name, '/') . '\b[^"]*"[^>]*>.*?<span[^>]+class="(?:level|amount)"[^>]*data-value="(\d+)"[^>]*>/s';

        $content = $response->getContent() ?: '';
        if (preg_match($pattern, $content, $matches)) {
            $actualLevel = $matches[1];
            if ($errorMessage !== '') {
                $this->assertEquals($expectedLevel, $actualLevel, $errorMessage);
            } else {
                $this->assertEquals($expectedLevel, $actualLevel, $object->title . ' is at level (' . $actualLevel . ') while it is expected to be at level (' . $expectedLevel . ').');
            }
        } else {
            $this->fail('No matching level found on page for object ' . $object->title);
        }
    }

    /**
     * Assert that the resources are as expected on the page.
     */
    protected function assertResourcesOnPage(TestResponse $response, Resources $resources): void
    {
        $content = $response->getContent() ?: '';

        if ($resources->metal->get() > 0) {
            $this->assertResourceOnPage($content, 'metal', $resources->metal->getFormattedLong());
        }

        if ($resources->crystal->get() > 0) {
            $this->assertResourceOnPage($content, 'crystal', $resources->crystal->getFormattedLong());
        }

        if ($resources->deuterium->get() > 0) {
            $this->assertResourceOnPage($content, 'deuterium', $resources->deuterium->getFormattedLong());
        }

        if ($resources->energy->get() > 0) {
            $this->assertResourceOnPage($content, 'energy', $resources->energy->getFormattedLong());
        }
    }

    /**
     * Assert that a single resource span is rendered with the expected formatted value.
     */
    private function assertResourceOnPage(string $content, string $resource, string $formattedValue): void
    {
        $pattern = '/<span\s+id="resources_' . $resource . '"\s+class="[^"]*"\s+data-raw="[^"]*">\s*' . preg_quote($formattedValue, '/') . '\s*<\/span>/';
        $this->assertTrue(preg_match($pattern, $content) === 1, 'Resource ' . $resource . ' is not at ' . $formattedValue . '.');
    }

    protected function assertObjectInQueue(TestResponse $response, string $machineName, int $level, string $errorMessage = ''): void
    {
        try {
            $object = ObjectService::getObjectByMachineName($machineName);
        } catch (Exception $e) {
            $this->fail('Failed to get object by machine name: ' . $machineName . '. Error: ' . $e->getMessage());
        }

        try {
            $responseContent = $response->getContent() ?: '';
            $condition1 = str_contains($responseContent, 'Cancel production of ' . $object->title . ' level ' . $level);
            $condition2 = str_contains($responseContent, 'do you really want to cancel ' . $object->title);
            $this->assertTrue($condition1 || $condition2, 'Neither of the expected texts were found in the response.');
        } catch (Exception $e) {
            if ($errorMessage !== '') {
                $this->fail($errorMessage . '. Error: ' . $e->getMessage());
            } else {
                $this->fail('Object ' . $object->title . ' is not in the queue. Error: ' . $e->getMessage());
            }
        }
    }

    protected function assertObjectNotInQueue(TestResponse $response, string $machineName, string $errorMessage = ''): void
    {
        try {
            $object = ObjectService::getObjectByMachineName($machineName);
        } catch (Exception $e) {
            $this->fail('Failed to get object by machine name: ' . $machineName . '. Error: ' . $e->getMessage());
        }

        try {
            $response->assertDontSee(['Cancel production of ' . $object->title, 'cancel ' . $object->title]);
        } catch (Exception $e) {
            if ($errorMessage !== '') {
                $this->fail($errorMessage . '. Error: ' . $e->getMessage());
            } else {
                $this->fail('Object ' . $object->title . ' is not in the queue. Error: ' . $e->getMessage());
            }
        }
    }

    protected function assertEmptyBuildingQueue(TestResponse $response, string $errorMessage = ''): void
    {
        try {
            $responseContent = $response->getContent() ?: '';
            $condition = str_contains($responseContent, 'no building being built');
            $this->assertTrue($condition, 'expected text was not found in the response.');
        } catch (Exception $e) {
            if ($errorMessage !== '') {
                $this->fail($errorMessage . '. Error: ' . $e->getMessage());
            } else {
                $this->fail('Building queue is not empty. Error: ' . $e->getMessage());
            }
        }
    }

    protected function assertEmptyResearchQueue(TestResponse $response, string $errorMessage = ''): void
    {
        try {
            $responseContent = $response->getContent() ?: '';
            $condition = str_contains($responseContent, 'no research done');
            $this->assertTrue($condition, 'expected text was not found in the response.');
        } catch (Exception $e) {
            if ($errorMessage !== '') {
                $this->fail($errorMessage . '. Error: ' . $e->getMessage());
            } else {
                $this->fail('Research queue is not empty. Error: ' . $e->getMessage());
            }
        }
    }

    protected function assertRequirementsNotMet(TestResponse $response, string $machineName, string $errorMessage = ''): void
    {
        try {
            $object = ObjectService::getObjectByMachineName($machineName);
        } catch (Exception $e) {
            $this->fail('Failed to get object by machine name: ' . $machineName . '. Error: ' . $e->getMessage());
        }

        try {
            $responseContent = $response->getContent() ?: '';
            $condition = str_contains($responseContent, $object->title . '<br/>Requirements are not met!');
            $this->assertTrue($condition, 'expected text was not found in the response.');
        } catch (Exception $e) {
            if ($errorMessage !== '') {
                $this->fail($errorMessage . '. Error: ' . $e->getMessage());
            } else {
                $this->fail('Requirements are met. Error: ' . $e->getMessage());
            }
        }
    }
}
