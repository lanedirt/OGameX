<?php

namespace Tests\Feature;

use OGame\Models\FleetTemplate;
use OGame\Models\User;
use Tests\AccountTestCase;

/**
 * Test fleet templates functionality.
 */
class FleetTemplatesTest extends AccountTestCase
{
    /**
     * Test that getting templates returns empty array initially.
     */
    public function testGetTemplatesInitiallyEmpty(): void
    {
        $response = $this->get('/ajax/fleet/templates');

        $response->assertStatus(200);
        $response->assertJson([
            'templates' => [],
        ]);
    }

    /**
     * Test creating a fleet template.
     */
    public function testCreateFleetTemplate(): void
    {
        $templateData = [
            'template_name' => 'Attack Fleet',
            'ship' => [
                '204' => 100, // Light Fighter
                '205' => 50,  // Heavy Fighter
                '206' => 25,  // Cruiser
            ],
        ];

        $response = $this->post('/ajax/fleet/templates', $templateData);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Template saved successfully.',
        ]);

        // Verify the template is in the database
        $this->assertDatabaseHas('fleet_templates', [
            'user_id' => $this->currentUserId,
            'name' => 'Attack Fleet',
        ]);
    }

    /**
     * Test that creating a template with empty name fails.
     */
    public function testCreateTemplateNameRequired(): void
    {
        $templateData = [
            'template_name' => '',
            'ship' => ['204' => 100],
        ];

        $response = $this->post('/ajax/fleet/templates', $templateData);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => false,
            'message' => 'Template name is required.',
        ]);
    }

    /**
     * Test that creating a template with no ships fails.
     */
    public function testCreateTemplateMustHaveShips(): void
    {
        $templateData = [
            'template_name' => 'Empty Fleet',
            'ship' => [],
        ];

        $response = $this->post('/ajax/fleet/templates', $templateData);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => false,
            'message' => 'Template must contain at least one ship.',
        ]);
    }

    /**
     * Test that invalid ship IDs are filtered out (not an error).
     */
    public function testCreateTemplateInvalidShipIdsFiltered(): void
    {
        $templateData = [
            'template_name' => 'Mixed Fleet',
            'ship' => [
                '204' => 100, // Valid
                '999' => 50,  // Invalid - will be filtered
            ],
        ];

        $response = $this->post('/ajax/fleet/templates', $templateData);

        // Should succeed with only valid ships
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        // Verify only valid ships were saved
        $template = FleetTemplate::where('user_id', $this->currentUserId)
            ->where('name', 'Mixed Fleet')
            ->first();
        $this->assertNotNull($template);
        $this->assertEquals(['204' => 100], $template->ships);
    }

    /**
     * Test that a user cannot create more than 10 templates.
     */
    public function testCreateTemplateMaxLimit(): void
    {
        // Create 10 templates
        for ($i = 1; $i <= 10; $i++) {
            FleetTemplate::factory()->create([
                'user_id' => $this->currentUserId,
                'name' => 'Template ' . $i,
                'ships' => ['204' => $i * 10],
            ]);
        }

        // Try to create the 11th template
        $templateData = [
            'template_name' => 'Template 11',
            'ship' => ['204' => 100],
        ];

        $response = $this->post('/ajax/fleet/templates', $templateData);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => false,
            'message' => 'Maximum number of templates reached (10).',
        ]);
    }

    /**
     * Test getting templates returns created templates.
     */
    public function testGetTemplatesReturnsUserTemplates(): void
    {
        // Create some templates
        FleetTemplate::factory()->create([
            'user_id' => $this->currentUserId,
            'name' => 'Fleet Alpha',
            'ships' => ['204' => 100, '205' => 50],
        ]);

        FleetTemplate::factory()->create([
            'user_id' => $this->currentUserId,
            'name' => 'Fleet Beta',
            'ships' => ['206' => 25, '207' => 10],
        ]);

        $response = $this->get('/ajax/fleet/templates');

        $response->assertStatus(200);
        $json = $response->json();

        $this->assertCount(2, $json['templates']);
        $this->assertEquals('Fleet Alpha', $json['templates'][0]['name']);
        $this->assertEquals('Fleet Beta', $json['templates'][1]['name']);
    }

    /**
     * Test that users only see their own templates.
     */
    public function testGetTemplatesUserScoped(): void
    {
        // Create a template for current user
        FleetTemplate::factory()->create([
            'user_id' => $this->currentUserId,
            'name' => 'My Template',
            'ships' => ['204' => 100],
        ]);

        // Create templates for another user
        $otherUser = User::factory()->create();
        FleetTemplate::factory()->create([
            'user_id' => $otherUser->id,
            'name' => 'Other User Template',
            'ships' => ['205' => 50],
        ]);

        $response = $this->get('/ajax/fleet/templates');

        $response->assertStatus(200);
        $json = $response->json();

        // Should only see one template (the current user's)
        $this->assertCount(1, $json['templates']);
        $this->assertEquals('My Template', $json['templates'][0]['name']);
        $this->assertNotEquals('Other User Template', $json['templates'][0]['name']);
    }

    /**
     * Test deleting a fleet template.
     */
    public function testDeleteFleetTemplate(): void
    {
        // Create a template
        $template = FleetTemplate::factory()->create([
            'user_id' => $this->currentUserId,
            'name' => 'To Delete',
            'ships' => ['204' => 100],
        ]);

        $response = $this->delete('/ajax/fleet/templates/' . $template->id);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Template deleted successfully.',
        ]);

        // Verify the template is gone
        $this->assertDatabaseMissing('fleet_templates', [
            'id' => $template->id,
        ]);
    }

    /**
     * Test that users cannot delete templates they don't own.
     */
    public function testDeleteTemplateUnauthorized(): void
    {
        // Create a template for another user
        $otherUser = User::factory()->create();
        $template = FleetTemplate::factory()->create([
            'user_id' => $otherUser->id,
            'name' => 'Other User Template',
            'ships' => ['204' => 100],
        ]);

        $response = $this->delete('/ajax/fleet/templates/' . $template->id);

        // Returns 200 with success: false (not 403)
        $response->assertStatus(200);
        $response->assertJson([
            'success' => false,
            'message' => 'Template not found.',
        ]);

        // Verify the template still exists
        $this->assertDatabaseHas('fleet_templates', [
            'id' => $template->id,
        ]);
    }

    /**
     * Test that JSON casting works correctly for ships field.
     */
    public function testTemplateShipsJsonCasting(): void
    {
        $ships = ['204' => 100, '205' => 50, '206' => 25];

        FleetTemplate::factory()->create([
            'user_id' => $this->currentUserId,
            'name' => 'Json Test',
            'ships' => $ships,
        ]);

        $response = $this->get('/ajax/fleet/templates');

        $response->assertStatus(200);
        $json = $response->json();

        $this->assertCount(1, $json['templates']);
        $this->assertEquals($ships, $json['templates'][0]['ships']);
    }

    /**
     * Test that ships are stored with correct data types (integers).
     */
    public function testTemplateShipsIntegerValues(): void
    {
        $templateData = [
            'template_name' => 'Integer Test',
            'ship' => [
                '204' => '100', // String that should be converted to int
                '205' => 50,    // Already int
            ],
        ];

        $response = $this->post('/ajax/fleet/templates', $templateData);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        // Retrieve the template from database and check the ships field
        $template = FleetTemplate::where('user_id', $this->currentUserId)
            ->where('name', 'Integer Test')
            ->first();

        $this->assertNotNull($template);
        $this->assertIsArray($template->ships);
        $this->assertEquals(100, $template->ships['204']);
        $this->assertEquals(50, $template->ships['205']);
    }

    /**
     * Test that updating an existing template works.
     */
    public function testUpdateExistingTemplate(): void
    {
        // Create a template
        $template = FleetTemplate::factory()->create([
            'user_id' => $this->currentUserId,
            'name' => 'Original Name',
            'ships' => ['204' => 100],
        ]);

        // Update it
        $templateData = [
            'template_id' => $template->id,
            'template_name' => 'Updated Name',
            'ship' => ['205' => 50],
        ];

        $response = $this->post('/ajax/fleet/templates', $templateData);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Template saved successfully.',
        ]);

        // Verify the update
        $template->refresh();
        $this->assertEquals('Updated Name', $template->name);
        $this->assertEquals(['205' => 50], $template->ships);
    }

    /**
     * Test that updating another user's template fails.
     */
    public function testUpdateOtherUserTemplateFails(): void
    {
        // Create a template for another user
        $otherUser = User::factory()->create();
        $template = FleetTemplate::factory()->create([
            'user_id' => $otherUser->id,
            'name' => 'Other Template',
            'ships' => ['204' => 100],
        ]);

        // Try to update it
        $templateData = [
            'template_id' => $template->id,
            'template_name' => 'Hacked Name',
            'ship' => ['205' => 50],
        ];

        $response = $this->post('/ajax/fleet/templates', $templateData);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => false,
            'message' => 'Template not found.',
        ]);

        // Verify the template wasn't changed
        $template->refresh();
        $this->assertEquals('Other Template', $template->name);
    }

    /**
     * Test that the fleet page loads correctly.
     */
    public function testFleetPageLoads(): void
    {
        $response = $this->get('/fleet');

        $response->assertStatus(200);
        $response->assertSee('Standard fleets');
    }
}
