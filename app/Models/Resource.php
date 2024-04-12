<?php

namespace OGame\Models;

use OGame\Facades\AppUtil;

class Resource
{
    protected int $rawValue = 0;

    public function __construct(int $rawValue)
    {
        $this->rawValue = $rawValue;
    }

    /**
     * Add another resource to this one.
     *
     * @param Resource $other
     * @return void
     */
    public function add(Resource $other): void {
        $this->rawValue += $other->get();
    }

    /**
     * Multiply resource by a factor.
     *
     * @param int $factor
     * @return void
     */
    public function multiply(int $factor): void {
        $this->rawValue = $this->rawValue * $factor;
    }

    /**
     * Get the raw value of the resource as integer.
     *
     * @return int
     */
    public function get(): int
    {
        return $this->rawValue;
    }

    /**
     * Get the formatted value of the resource as string (short, default).
     *
     * @return string
     */
    public function getFormatted(): string
    {
        return AppUtil::formatNumberShort($this->rawValue);
    }

    /**
     * Get the formatted value of the resource as string (long).
     *
     * @return string
     */
    public function getFormattedLong(): string
    {
        return AppUtil::formatNumberLong($this->rawValue);
    }

    /**
     * Set the raw value of the resource.
     *
     * @param int $value
     * @return void
     */
    public function set(int $value): void
    {
        $this->rawValue = $value;
    }
}