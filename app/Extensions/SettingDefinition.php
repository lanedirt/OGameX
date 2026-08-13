<?php

namespace OGame\Extensions;

/**
 * Fluent definition of a single module-scoped server setting.
 *
 * A module declares a setting through the ModuleExtension builder and OGameX
 * persists it under the "{module_alias}.{key}" namespace, validates values in
 * the admin UI, and reads them back through SettingsService::module().
 */
class SettingDefinition
{
    public string $type = 'string';

    public string|int|float|bool $default = '';

    public int|float|null $min = null;

    public int|float|null $max = null;

    public string $label = '';

    public string $description = '';

    /**
     * Additional Laravel validation rules appended after the type/min/max rules.
     *
     * @var array<int, string>
     */
    public array $rules = [];

    public function __construct(public readonly string $key)
    {
    }

    public function string(): static
    {
        $this->type = 'string';

        return $this;
    }

    public function integer(): static
    {
        $this->type = 'integer';

        return $this;
    }

    public function boolean(): static
    {
        $this->type = 'boolean';

        return $this;
    }

    public function default(string|int|float|bool $value): static
    {
        $this->default = $value;

        return $this;
    }

    public function min(int|float $value): static
    {
        $this->min = $value;

        return $this;
    }

    public function max(int|float $value): static
    {
        $this->max = $value;

        return $this;
    }

    public function label(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function description(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Append additional Laravel validation rules.
     *
     * @param array<int, string> $rules
     */
    public function rules(array $rules): static
    {
        $this->rules = $rules;

        return $this;
    }

    /**
     * The validation rules used when the admin UI saves this setting.
     *
     * @return array<int, string>
     */
    public function validationRules(): array
    {
        $rules = [$this->type];

        if ($this->min !== null) {
            $rules[] = 'min:' . $this->min;
        }

        if ($this->max !== null) {
            $rules[] = 'max:' . $this->max;
        }

        return [...$rules, ...$this->rules];
    }
}
