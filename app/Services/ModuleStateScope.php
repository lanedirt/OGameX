<?php

namespace OGame\Services;

use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use JsonException;
use OGame\Models\ModuleState;

/** A single module namespace scoped to the server, a planet, or a player. */
class ModuleStateScope
{
    public function __construct(
        private readonly string $alias,
        private readonly string $scope,
        private readonly int $ownerId,
    ) {
        if ($ownerId < 0) {
            throw new InvalidArgumentException('Module state owner IDs cannot be negative.');
        }
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $value = DB::table('module_states')->where($this->identity($key))->value('value');

        if ($value === null) {
            return $default;
        }

        return json_decode($value, true, 512, JSON_THROW_ON_ERROR);
    }

    /** @throws JsonException */
    public function put(string $key, mixed $value): void
    {
        ModuleState::query()->updateOrCreate(
            $this->identity($key),
            ['value' => json_encode($value, JSON_THROW_ON_ERROR)],
        );
    }

    public function forget(string $key): void
    {
        DB::table('module_states')->where($this->identity($key))->delete();
    }

    /** @return array<string, mixed> */
    public function all(): array
    {
        return DB::table('module_states')
            ->where(['module_alias' => $this->alias, 'scope' => $this->scope, 'owner_id' => $this->ownerId])
            ->pluck('value', 'key')
            ->map(fn (string $value): mixed => json_decode($value, true, 512, JSON_THROW_ON_ERROR))
            ->all();
    }

    /** @return array<string, int|string> */
    private function identity(string $key): array
    {
        if (!preg_match('/^[a-z][a-z0-9_.:-]*$/', $key)) {
            throw new InvalidArgumentException('Module state keys must be lowercase, namespaced identifiers.');
        }

        return ['module_alias' => $this->alias, 'scope' => $this->scope, 'owner_id' => $this->ownerId, 'key' => $key];
    }
}
