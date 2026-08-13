<?php

namespace OGame\Services;

use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use OGame\Extensions\ExtensionRegistry;
use OGame\GameObjects\Models\Abstracts\GameObject;
use OGame\Models\ModuleObjectState;
use OGame\Models\ModuleState;
use OGame\Models\Planet;
use OGame\Models\User;
use RuntimeException;

/**
 * Storage adapter for module-contributed game objects.
 *
 * It is intentionally used only for registered module objects. This keeps the
 * mature core schema and its hot-path columns intact while allowing modules to
 * add buildings, research, ships, and defence without a core migration.
 */
class ModuleObjectStateService
{
    public function __construct(private readonly ExtensionRegistry $extensions)
    {
    }

    public function manages(GameObject $object): bool
    {
        return $this->extensions->objectOwner($object) !== null;
    }

    public function planetAmount(Planet $planet, GameObject $object): int
    {
        return $this->amount($this->owner($object), 'planet', $planet->id, $object);
    }

    public function setPlanetAmount(Planet $planet, GameObject $object, int $amount): void
    {
        $this->setAmount($this->owner($object), 'planet', $planet->id, $object, $amount);
    }

    /**
     * Read an object-specific planet option without introducing a second core
     * column family (for example, a module production percentage).
     */
    public function planetOption(Planet $planet, GameObject $object, string $key, int $default = 0): int
    {
        $value = DB::table('module_states')
            ->where($this->stateIdentity($this->owner($object), $planet->id, $object, $key))
            ->value('value');

        return $value === null ? $default : (int) json_decode($value, true, 512, JSON_THROW_ON_ERROR);
    }

    public function setPlanetOption(Planet $planet, GameObject $object, string $key, int $value): void
    {
        ModuleState::query()->updateOrCreate(
            $this->stateIdentity($this->owner($object), $planet->id, $object, $key),
            ['value' => json_encode($value, JSON_THROW_ON_ERROR)],
        );
    }

    public function incrementPlanetAmount(Planet $planet, GameObject $object, int $amount): int
    {
        return $this->increment($this->owner($object), 'planet', $planet->id, $object, $amount);
    }

    public function decrementPlanetAmount(Planet $planet, GameObject $object, int $amount): int
    {
        return $this->decrement($this->owner($object), 'planet', $planet->id, $object, $amount);
    }

    public function playerAmount(User $player, GameObject $object): int
    {
        return $this->amount($this->owner($object), 'player', $player->id, $object);
    }

    public function setPlayerAmount(User $player, GameObject $object, int $amount): void
    {
        $this->setAmount($this->owner($object), 'player', $player->id, $object, $amount);
    }

    private function amount(string $alias, string $scope, int $ownerId, GameObject $object): int
    {
        return (int) DB::table('module_object_states')
            ->where($this->identity($alias, $scope, $ownerId, $object))
            ->value('amount');
    }

    private function setAmount(string $alias, string $scope, int $ownerId, GameObject $object, int $amount): void
    {
        if ($amount < 0) {
            throw new InvalidArgumentException('Module game object amounts cannot be negative.');
        }

        ModuleObjectState::query()->updateOrCreate(
            $this->identity($alias, $scope, $ownerId, $object),
            ['object_id' => $object->id, 'amount' => $amount],
        );
    }

    private function increment(string $alias, string $scope, int $ownerId, GameObject $object, int $amount): int
    {
        if ($amount < 0) {
            throw new InvalidArgumentException('Use decrementPlanetAmount() to remove module units.');
        }

        $identity = $this->identity($alias, $scope, $ownerId, $object);
        ModuleObjectState::query()->updateOrCreate(
            $identity,
            ['object_id' => $object->id],
        );
        DB::table('module_object_states')->where($identity)->increment('amount', $amount, ['updated_at' => now()]);

        return $this->amount($alias, $scope, $ownerId, $object);
    }

    private function decrement(string $alias, string $scope, int $ownerId, GameObject $object, int $amount): int
    {
        if ($amount < 1) {
            throw new InvalidArgumentException('The amount to remove must be at least one.');
        }

        $identity = $this->identity($alias, $scope, $ownerId, $object);
        $affected = DB::table('module_object_states')
            ->where($identity)
            ->where('amount', '>=', $amount)
            ->decrement('amount', $amount, ['updated_at' => now()]);

        if ($affected === 0) {
            throw new RuntimeException('Planet does not have enough units.');
        }

        return $this->amount($alias, $scope, $ownerId, $object);
    }

    /** @return array<string, int|string> */
    private function identity(string $alias, string $scope, int $ownerId, GameObject $object): array
    {
        return [
            'module_alias' => $alias,
            'scope' => $scope,
            'owner_id' => $ownerId,
            'machine_name' => $object->machine_name,
        ];
    }

    private function owner(GameObject $object): string
    {
        $alias = $this->extensions->objectOwner($object);

        if ($alias === null) {
            throw new RuntimeException(sprintf('[%s] is not a module game object.', $object->machine_name));
        }

        return $alias;
    }

    /** @return array<string, int|string> */
    private function stateIdentity(string $alias, int $planetId, GameObject $object, string $key): array
    {
        if (!preg_match('/^[a-z][a-z0-9_]*$/', $key)) {
            throw new InvalidArgumentException('Module object option keys must be lowercase snake_case.');
        }

        return [
            'module_alias' => $alias,
            'scope' => 'planet',
            'owner_id' => $planetId,
            'key' => sprintf('objects.%s.%s', $object->machine_name, $key),
        ];
    }
}
