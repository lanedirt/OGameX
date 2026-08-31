<?php

namespace Tests\Traits;

use Exception;
use OGame\Services\ObjectService;

/**
 * Helpers for submitting and canceling build requests through the ingame HTTP endpoints
 * (resources, facilities, research, shipyard, defense).
 *
 * @method \Illuminate\Testing\TestResponse post(string $uri, array $data = [], array $headers = [])
 */
trait SubmitsBuildRequests
{
    /**
     * Add a resource build request to the current users current planet.
     *
     * @throws Exception
     */
    protected function addResourceBuildRequest(string $machineName, bool $ignoreErrors = false): void
    {
        $object = ObjectService::getObjectByMachineName($machineName);

        $response = $this->post('/resources/add-buildrequest', [
            '_token' => csrf_token(),
            'technologyId' => $object->id,
        ]);

        if ($ignoreErrors) {
            return;
        }

        $response->assertStatus(200);
    }

    /**
     * Cancel a resource build request on the current users current planet.
     */
    protected function cancelResourceBuildRequest(int $objectId, int $buildQueueId): void
    {
        $response = $this->post('/resources/cancel-buildrequest', [
            '_token' => csrf_token(),
            'technologyId' => $objectId,
            'listId' => $buildQueueId,
        ]);

        $response->assertStatus(200);
    }

    /**
     * Add a facilities build request to the current users current planet.
     *
     * @throws Exception
     */
    protected function addFacilitiesBuildRequest(string $machineName): void
    {
        $object = ObjectService::getObjectByMachineName($machineName);

        $response = $this->post('/facilities/add-buildrequest', [
            '_token' => csrf_token(),
            'technologyId' => $object->id,
        ]);

        $response->assertStatus(200);
    }

    /**
     * Cancel a facilities build request on the current users current planet.
     */
    protected function cancelFacilitiesBuildRequest(int $objectId, int $buildQueueId): void
    {
        $response = $this->post('/facilities/cancel-buildrequest', [
            '_token' => csrf_token(),
            'technologyId' => $objectId,
            'listId' => $buildQueueId,
        ]);

        $response->assertStatus(200);
    }

    /**
     * Add a research build request to the current users current planet.
     */
    protected function addResearchBuildRequest(string $machineName): void
    {
        $object = ObjectService::getObjectByMachineName($machineName);

        $response = $this->post('/research/add-buildrequest', [
            '_token' => csrf_token(),
            'technologyId' => $object->id,
        ]);

        $response->assertStatus(200);
    }

    /**
     * Cancel a research build request on the current users current planet.
     */
    protected function cancelResearchBuildRequest(int $objectId, int $buildQueueId): void
    {
        $response = $this->post('/research/cancel-buildrequest', [
            '_token' => csrf_token(),
            'technologyId' => $objectId,
            'listId' => $buildQueueId,
        ]);

        $response->assertStatus(200);
    }

    /**
     * Add a shipyard build request to the current users current planet.
     *
     * @throws Exception
     */
    protected function addShipyardBuildRequest(string $machineName, int $amount): void
    {
        $object = ObjectService::getObjectByMachineName($machineName);

        $response = $this->post('/shipyard/add-buildrequest', [
            '_token' => csrf_token(),
            'technologyId' => $object->id,
            'amount' => $amount,
        ]);

        $response->assertStatus(200);
    }

    /**
     * Add a defense build request to the current users current planet.
     *
     * @throws Exception
     */
    protected function addDefenseBuildRequest(string $machineName, int $amount): void
    {
        $object = ObjectService::getObjectByMachineName($machineName);

        $response = $this->post('/defense/add-buildrequest', [
            '_token' => csrf_token(),
            'technologyId' => $object->id,
            'amount' => $amount,
        ]);

        $response->assertStatus(200);
    }
}
