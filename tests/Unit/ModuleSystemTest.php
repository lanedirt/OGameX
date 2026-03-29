<?php

namespace Tests\Unit;

use OGame\Modules\ModuleServiceProvider;
use OGame\Services\ModuleSlotService;
use Tests\TestCase;

/**
 * Tests for the OGameX module system infrastructure.
 *
 * Covers: discovery registry, slot rendering, enable/disable state.
 */
class ModuleSystemTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        ModuleServiceProvider::resetDiscovered();
        ModuleSlotService::resetSlots();
    }

    protected function tearDown(): void
    {
        ModuleServiceProvider::resetDiscovered();
        ModuleSlotService::resetSlots();
        parent::tearDown();
    }

    // ── Discovery registry ────────────────────────────────────────────────────

    public function test_module_is_registered_in_discovery_on_instantiation(): void
    {
        $this->assertNotContains(StubModuleProvider::class, ModuleServiceProvider::allDiscovered());

        new StubModuleProvider(app());

        $this->assertContains(StubModuleProvider::class, ModuleServiceProvider::allDiscovered());
    }

    public function test_multiple_modules_are_all_registered(): void
    {
        new StubModuleProvider(app());
        new AnotherStubModuleProvider(app());

        $discovered = ModuleServiceProvider::allDiscovered();

        $this->assertContains(StubModuleProvider::class, $discovered);
        $this->assertContains(AnotherStubModuleProvider::class, $discovered);
    }

    public function test_reset_discovered_clears_registry(): void
    {
        new StubModuleProvider(app());
        $this->assertNotEmpty(ModuleServiceProvider::allDiscovered());

        ModuleServiceProvider::resetDiscovered();

        $this->assertEmpty(ModuleServiceProvider::allDiscovered());
    }

    // ── Slot service ─────────────────────────────────────────────────────────

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
        ModuleSlotService::register('test.slot', fn (array $data): string => '<div>hello</div>');

        $this->assertSame('<div>hello</div>', ModuleSlotService::render('test.slot'));
    }

    public function test_has_slot_returns_true_after_registration(): void
    {
        ModuleSlotService::register('test.slot', fn (array $data): string => '');

        $this->assertTrue(ModuleSlotService::hasSlot('test.slot'));
    }

    public function test_multiple_renderers_for_same_slot_are_concatenated(): void
    {
        ModuleSlotService::register('test.slot', fn (array $data): string => 'A');
        ModuleSlotService::register('test.slot', fn (array $data): string => 'B');

        $this->assertSame('AB', ModuleSlotService::render('test.slot'));
    }

    public function test_renderer_receives_data_array(): void
    {
        ModuleSlotService::register('test.slot', fn (array $data): string => $data['key'] ?? 'missing');

        $this->assertSame('value', ModuleSlotService::render('test.slot', ['key' => 'value']));
    }

    public function test_reset_slots_clears_all_renderers(): void
    {
        ModuleSlotService::register('test.slot', fn (array $data): string => 'x');
        $this->assertTrue(ModuleSlotService::hasSlot('test.slot'));

        ModuleSlotService::resetSlots();

        $this->assertFalse(ModuleSlotService::hasSlot('test.slot'));
        $this->assertSame('', ModuleSlotService::render('test.slot'));
    }

    public function test_renderers_in_different_slots_do_not_interfere(): void
    {
        ModuleSlotService::register('slot.a', fn (array $data): string => 'A');
        ModuleSlotService::register('slot.b', fn (array $data): string => 'B');

        $this->assertSame('A', ModuleSlotService::render('slot.a'));
        $this->assertSame('B', ModuleSlotService::render('slot.b'));
    }
}

// ── Stub module providers used only in this test file ────────────────────────

class StubModuleProvider extends ModuleServiceProvider
{
    public function moduleId(): string
    {
        return 'stub';
    }

    public function modulePath(string $relative): string
    {
        return __DIR__ . '/' . $relative;
    }

    public function bootModule(): void
    {
    }
}

class AnotherStubModuleProvider extends ModuleServiceProvider
{
    public function moduleId(): string
    {
        return 'another-stub';
    }

    public function modulePath(string $relative): string
    {
        return __DIR__ . '/' . $relative;
    }

    public function bootModule(): void
    {
    }
}
