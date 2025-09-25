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
     * @param float $factor
     * @return void
     */
    public function multiply(float $factor): void
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
     * @return string
     */
    public function getFormatted(): string
    {
        return AppUtil::formatNumberShort($this->rawValue);
    }

    /**
     * Get the formatted value of the resource as string (longer, e.g. 5.33M).
     *
     * @return string
     */
    public function getFormattedLong(): string
    {
        return AppUtil::formatNumberLong($this->rawValue);
    }

    /**
     * Get the formatted value of the resource as string (all digits, no shortening e.g. 5,000,000).
     *
     * @return string
     */
    public function getFormattedFull(): string
    {
        return AppUtil::formatNumber($this->rawValue);
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
