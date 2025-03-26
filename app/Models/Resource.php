<?php

namespace OGame\Models;

use OGame\Facades\AppUtil;

class Resource
{
    protected float $rawValue = 0;

    public function __construct(float $rawValue)
    {
        $this->rawValue = $rawValue;
    }

    /**
     * Add another resource to this one.
     *
     * @param Resource $other
     * @return void
     */
    public function add(Resource $other): void
    {
        $this->rawValue += $other->get();
    }

    /**
     * Multiply resource by a factor.
     *
     * @param int $factor
     * @return void
     */
    public function multiply(int $factor): void
    {
        $this->rawValue = $this->rawValue * $factor;
    }

    /**
     * Get the raw value of the resource as integer.
     *
     * @return float
     */
    public function get(): float
    {
        return $this->rawValue;
    }

    /**
     * Get the rounded (floored) value of the resource as integer.
     *
     * @return int
     */
    public function getRounded(): int
    {
        return (int) round($this->rawValue);
    }

    /**
     * Get the formatted value of the resource as string (short, e.g. 5M).
     *
     * @param float|null $multiplier
     * @return string
     */
    public function getFormatted(?float $multiplier = 1): string
    {
        return AppUtil::formatNumberShort($this->rawValue * $multiplier);
    }

    /**
     * Get the formatted value of the resource as string (longer, e.g. 5.33M).
     *
     * @param float|null $multiplier
     * @return string
     */
    public function getFormattedLong(?float $multiplier = 1): string
    {
        return AppUtil::formatNumberLong($this->rawValue * $multiplier);
    }

    /**
     * Get the formatted value of the resource as string (all digits, no shortening e.g. 5,000,000).
     *
     * @param float|null $multiplier
     * @return string
     */
    public function getFormattedFull(?float $multiplier = 1): string
    {
        return AppUtil::formatNumber($this->rawValue * $multiplier);
    }

    /**
     * Set the raw value of the resource.
     *
     * @param float $value
     * @return void
     */
    public function set(float $value): void
    {
        $this->rawValue = $value;
    }
}
