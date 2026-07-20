<?php

namespace Tests\Feature;

use DOMDocument;
use DOMXPath;
use Illuminate\Support\Facades\DB;
use OGame\Models\WreckField;
use Tests\AccountTestCase;

/**
 * Sidebar wreck field icon must key off each listed planet's Space Dock,
 * not the currently selected planet. Single-planet coverage cannot catch this.
 */
class SidebarWreckFieldIconTest extends AccountTestCase
{
    protected function tearDown(): void
    {
        WreckField::where('owner_player_id', $this->currentUserId)->delete();

        parent::tearDown();
    }

    /**
     * Wreck + Space Dock on planet A, planet B selected without Space Dock:
     * icon must still appear on A's sidebar entry.
     */
    public function test_sidebar_shows_wreck_field_icon_for_planet_with_space_dock_when_other_planet_selected(): void
    {
        $this->assertNotNull($this->secondPlanetService);

        $this->setPlanetSpaceDock($this->planetService->getPlanetId(), 1);
        $this->setPlanetSpaceDock($this->secondPlanetService->getPlanetId(), 0);
        $this->createWreckFieldOnPlanet($this->planetService->getPlanetId());

        $this->switchToSecondPlanet();

        $response = $this->get('/overview?cp=' . $this->secondPlanetService->getPlanetId());
        $response->assertStatus(200);

        $html = (string) $response->getContent();

        $this->assertTrue(
            $this->sidebarPlanetHasWreckFieldIcon($html, $this->planetService->getPlanetId()),
            'Wreck field icon should appear on the planet that has both a wreck field and Space Dock, even when another planet is selected.'
        );
        $this->assertFalse(
            $this->sidebarPlanetHasWreckFieldIcon($html, $this->secondPlanetService->getPlanetId()),
            'Wreck field icon should not appear on the selected planet that has no wreck field.'
        );
    }

    /**
     * Wreck on planet A without Space Dock, planet B selected with Space Dock:
     * icon must not appear on A (would falsely show if checking current()).
     */
    public function test_sidebar_hides_wreck_field_icon_when_wreck_planet_lacks_space_dock(): void
    {
        $this->assertNotNull($this->secondPlanetService);

        $this->setPlanetSpaceDock($this->planetService->getPlanetId(), 0);
        $this->setPlanetSpaceDock($this->secondPlanetService->getPlanetId(), 1);
        $this->createWreckFieldOnPlanet($this->planetService->getPlanetId());

        $this->switchToSecondPlanet();

        $response = $this->get('/overview?cp=' . $this->secondPlanetService->getPlanetId());
        $response->assertStatus(200);

        $html = (string) $response->getContent();

        $this->assertFalse(
            $this->sidebarPlanetHasWreckFieldIcon($html, $this->planetService->getPlanetId()),
            'Wreck field icon must not appear when the wreck planet has no Space Dock, even if the selected planet does.'
        );
        $this->assertFalse(
            $this->sidebarPlanetHasWreckFieldIcon($html, $this->secondPlanetService->getPlanetId()),
            'Wreck field icon should not appear on the selected planet that has no wreck field.'
        );
    }

    private function setPlanetSpaceDock(int $planetId, int $level): void
    {
        DB::table('planets')
            ->where('id', $planetId)
            ->update(['space_dock' => $level]);
    }

    private function createWreckFieldOnPlanet(int $planetId): WreckField
    {
        $planet = DB::table('planets')->where('id', $planetId)->first();
        $this->assertNotNull($planet);

        return WreckField::factory()->create([
            'galaxy' => $planet->galaxy,
            'system' => $planet->system,
            'planet' => $planet->planet,
            'owner_player_id' => $this->currentUserId,
            'status' => 'active',
            'expires_at' => now()->addHours(72),
            'ship_data' => [
                ['machine_name' => 'light_fighter', 'quantity' => 10, 'repair_progress' => 0],
            ],
        ]);
    }

    private function sidebarPlanetHasWreckFieldIcon(string $html, int $planetId): bool
    {
        $dom = new DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new DOMXPath($dom);
        $nodes = $xpath->query(
            "//div[contains(concat(' ', normalize-space(@class), ' '), ' smallplanet ') and @data-planet-id='{$planetId}']//a[contains(concat(' ', normalize-space(@class), ' '), ' wreckFieldIcon ')]"
        );

        return $nodes !== false && $nodes->length > 0;
    }
}
