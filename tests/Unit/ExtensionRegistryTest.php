<?php

namespace Tests\Unit;

use InvalidArgumentException;
use OGame\Extensions\ExtensionRegistry;
use OGame\Extensions\ModuleExtension;
use OGame\GameObjects\Models\BuildingObject;
use OGame\Services\ModuleSlotService;
use Tests\TestCase;

class ExtensionRegistryTest extends TestCase
{
    private ExtensionRegistry $registry;

    protected function setUp(): void
    {
        parent::setUp();

        $this->registry = app(ExtensionRegistry::class);
        $this->registry->flush();
    }

    protected function tearDown(): void
    {
        $this->registry->flush();

        parent::tearDown();
    }

    public function test_module_registration_reuses_the_module_builder(): void
    {
        $first = $this->registry->module('lifeforms', function (ModuleExtension $module): void {
            $module->setting('population.tick_seconds')->integer()->default(60);
        });
        $second = $this->registry->module('lifeforms', function (ModuleExtension $module): void {
            $module->setting('population.max')->integer()->default(100);
        });

        $this->assertSame($first, $second);
        $this->assertArrayHasKey('lifeforms.population.tick_seconds', $this->registry->settings());
        $this->assertArrayHasKey('lifeforms.population.max', $this->registry->settings());
    }

    public function test_invalid_module_alias_is_rejected_early(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid module alias [Lifeforms]');

        $this->registry->module('Lifeforms', static function (ModuleExtension $module): void {
        });
    }

    public function test_duplicate_setting_key_is_rejected_early(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('already registered setting key [population.tick_seconds]');

        $this->registry->module('lifeforms', function (ModuleExtension $module): void {
            $module->setting('population.tick_seconds');
            $module->setting('population.tick_seconds');
        });
    }

    public function test_invalid_extension_contract_is_rejected_during_registration(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('must extend or implement');

        $this->registry->module('lifeforms', function (ModuleExtension $module): void {
            $module->extendPlanet(self::class);
        });
    }

    public function test_flush_removes_registered_slots_alongside_module_metadata(): void
    {
        $this->registry->module('lifeforms', function (ModuleExtension $module): void {
            $module->slot('admin.nav', static fn (array $data): string => 'lifeforms');
        });

        $this->assertSame('lifeforms', ModuleSlotService::render('admin.nav'));

        $this->registry->flush();

        $this->assertSame('', ModuleSlotService::render('admin.nav'));
    }

    public function test_module_object_has_a_stable_owner_namespace(): void
    {
        $object = new BuildingObject();
        $object->id = 10000;
        $object->machine_name = 'population_center';

        $this->registry->module('lifeforms', function (ModuleExtension $module) use ($object): void {
            $module->objects([$object]);
        });

        $this->assertSame('lifeforms', $this->registry->objectOwner($object));
    }

    public function test_module_object_ids_below_the_reserved_range_are_rejected(): void
    {
        $object = new BuildingObject();
        $object->id = 99;
        $object->machine_name = 'invalid_module_object';

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('must be 10000 or higher');

        $this->registry->module('lifeforms', function (ModuleExtension $module) use ($object): void {
            $module->objects([$object]);
        });
    }
}
