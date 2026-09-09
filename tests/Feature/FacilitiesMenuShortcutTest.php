<?php

namespace Tests\Feature;

use Tests\AccountTestCase;

class FacilitiesMenuShortcutTest extends AccountTestCase
{
    public function test_facilities_menu_shortcut_opens_space_dock_on_planet(): void
    {
        $response = $this->get(route('overview.index'));

        $response->assertStatus(200);

        $spaceDockUrl = route('facilities.index') . '?openSpaceDock=1';

        $response->assertSee(
            'href="' . $spaceDockUrl . '"',
            false
        );
    }
}
