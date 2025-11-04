<?php

namespace OGame\Factories;

use Illuminate\Contracts\Container\BindingResolutionException;
use OGame\Services\AllianceService;
use RuntimeException;

class AllianceServiceFactory
{
    /**
     * Cached instances of AllianceService.
     *
     * @var array<AllianceService>
     */
    protected array $instances = [];

    /**
     * Returns an AllianceService either from local instances cache or creates a new one.
     *
     * @param int $allianceId
     * @param bool $reloadCache Whether to force retrieve the object and reload the cache. Defaults to false.
     *
     * @return AllianceService
     */
    public function make(int $allianceId, bool $reloadCache = false): AllianceService
    {
        if ($reloadCache || !isset($this->instances[$allianceId])) {
            try {
                $allianceService = resolve(AllianceService::class, ['alliance_id' => $allianceId]);
                $this->instances[$allianceId] = $allianceService;
            } catch (BindingResolutionException $e) {
                throw new RuntimeException('Class not found: ' . AllianceService::class);
            }
        }

        return $this->instances[$allianceId];
    }
}
