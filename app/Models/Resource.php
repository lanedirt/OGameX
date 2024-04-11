<?php

namespace OGame\Models;

use OGame\Facades\AppUtil;

class Resource
{
    public int $rawValue = 0;

    public function __construct(int $rawValue)
    {
        $this->rawValue = $rawValue;
    }

    public function formatted(): string
    {
        return AppUtil::formatNumberShort($this->rawValue);
    }
}