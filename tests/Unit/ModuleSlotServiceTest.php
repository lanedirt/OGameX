<?php

namespace Tests\Unit;

use InvalidArgumentException;
use OGame\Services\ModuleSlotService;
use Tests\TestCase;

/**
 * Tests for the OGameX module view-slot injection service.
 */
class ModuleSlotServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        ModuleSlotService::resetSlots();
    }

    protected function tearDown(): void
    {
        ModuleSlotService::resetSlots();
        parent::tearDown();
    }

    public function test_slot_renders_empty_string_when_no_renderers_registered(): void
    {
        $this->assertSame('', ModuleSlotService::render('some.slot'));
    }

    public function test_has_slot_returns_false_when_no_renderers_registered(): void
    {
        $this->assertFalse(ModuleSlotService::hasSlot('some.slot'));
    }

    public function test_registered_renderer_is_called_on_render(): void
    {
        ModuleSlotService::register('admin.nav', fn (array $data): string => '<div>hello</div>');

        $this->assertSame('<div>hello</div>', ModuleSlotService::render('admin.nav'));
    }

    public function test_has_slot_returns_true_after_registration(): void
    {
        ModuleSlotService::register('admin.nav', fn (array $data): string => '');

        $this->assertTrue(ModuleSlotService::hasSlot('admin.nav'));
    }

    public function test_multiple_renderers_for_same_slot_are_concatenated(): void
    {
        ModuleSlotService::register('admin.nav', fn (array $data): string => 'A');
        ModuleSlotService::register('admin.nav', fn (array $data): string => 'B');

        $this->assertSame('AB', ModuleSlotService::render('admin.nav'));
    }

    public function test_renderer_receives_data_array(): void
    {
        ModuleSlotService::register('admin.nav', fn (array $data): string => $data['key'] ?? 'missing');

        $this->assertSame('value', ModuleSlotService::render('admin.nav', ['key' => 'value']));
    }

    public function test_reset_slots_clears_all_renderers(): void
    {
        ModuleSlotService::register('admin.nav', fn (array $data): string => 'x');
        $this->assertTrue(ModuleSlotService::hasSlot('admin.nav'));

        ModuleSlotService::resetSlots();

        $this->assertFalse(ModuleSlotService::hasSlot('admin.nav'));
        $this->assertSame('', ModuleSlotService::render('admin.nav'));
    }

    public function test_only_the_admin_nav_slot_is_supported(): void
    {
        $this->assertSame(['admin.nav'], ModuleSlotService::SLOTS);
    }

    public function test_unknown_slot_is_rejected_with_the_supported_slots(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown module view slot [test.slot]');

        ModuleSlotService::register('test.slot', fn (array $data): string => '');
    }
}
