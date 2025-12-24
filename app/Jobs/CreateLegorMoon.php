<?php

namespace OGame\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use OGame\Models\DebrisField;
use OGame\Models\Enums\PlanetType;
use OGame\Models\Planet;
use OGame\Models\Resources;

class CreateLegorMoon implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public function __construct(
        private int $planetId
    ) {}

    public function handle(): void {
        $planet = Planet::find($this->planetId);
        if (!$planet) {
            Log::warning("CreateLegorMoon: Planet {$this->planetId} not found");
            return;
        }

        // Check if moon already exists
        $existingMoon = Planet::where('galaxy', $planet->galaxy)
            ->where('system', $planet->system)
            ->where('planet', $planet->planet)
            ->where('planet_type', PlanetType::Moon->value)
            ->first();

        if ($existingMoon) {
            Log::info("CreateLegorMoon: Moon already exists for planet {$this->planetId}");
            return;
        }

        // Generate random debris field > 30k resources
        $debrisData = $this->generateRandomDebris();

        // Create or update debris field
        $debris = DebrisField::firstOrNew([
            'galaxy' => $planet->galaxy,
            'system' => $planet->system,
            'planet' => $planet->planet,
        ]);
        $debris->metal = ($debris->metal ?? 0) + $debrisData['metal'];
        $debris->crystal = ($debris->crystal ?? 0) + $debrisData['crystal'];
        $debris->deuterium = 0;
        $debris->save();

        $totalDebris = $debrisData['metal'] + $debrisData['crystal'];
        Log::info("CreateLegorMoon: Created debris field at {$planet->galaxy}:{$planet->system}:{$planet->planet} with {$totalDebris} resources");

        // Create moon
        $x = rand(10, 20);
        $diameter = (int)floor(pow($x + (3 * $totalDebris / 100000), 0.5) * 1000);

        $moon = new Planet();
        $moon->user_id = $planet->user_id;
        $moon->name = 'Moon';
        $moon->galaxy = $planet->galaxy;
        $moon->system = $planet->system;
        $moon->planet = $planet->planet;
        $moon->planet_type = PlanetType::Moon->value;
        $moon->diameter = $diameter;
        $moon->field_max = 1;
        $moon->field_current = 0;

        // Calculate temperature (average of planet range)
        $avgTemp = (int)(($planet->temp_min + $planet->temp_max) / 2);
        $moon->temp_max = $avgTemp;
        $moon->temp_min = $avgTemp - 40;

        // No resources on moon
        $moon->metal = 0;
        $moon->crystal = 0;
        $moon->deuterium = 0;

        // Time
        $moon->time_last_update = now()->timestamp;
        $moon->destroyed = 0;

        $moon->save();

        Log::info("CreateLegorMoon: Created moon for planet {$this->planetId} with diameter {$diameter}km");
    }

    private function generateRandomDebris(): array
    {
        // Total debris between 30,001 and 100,000
        $totalDebris = rand(30001, 100000);

        // Metal to crystal ratio between 55-75%
        $metalRatio = rand(55, 75) / 100;

        $metal = (int) round($totalDebris * $metalRatio);
        $crystal = $totalDebris - $metal;

        return [
            'metal' => $metal,
            'crystal' => $crystal,
        ];
    }
}
