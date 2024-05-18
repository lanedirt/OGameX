<?php

namespace Tests\Unit;

use OGame\Facades\AppUtil;
use PHPUnit\Framework\TestCase;

class NumberFormatTest extends TestCase
{
    /**
     * Test that number formatting works as expected.
     */
    public function testFormatNumberShort(): void
    {
        $this->assertEquals('1,000', AppUtil::formatNumberShort(1000));
        $this->assertEquals('1,0K', AppUtil::formatNumberShort(1001));
        $this->assertEquals('1,1K', AppUtil::formatNumberShort(1150));
        $this->assertEquals('1,5K', AppUtil::formatNumberShort(1500));
        $this->assertEquals('10K', AppUtil::formatNumberShort(10500));
        $this->assertEquals('50K', AppUtil::formatNumberShort(50000));
        $this->assertEquals('125K', AppUtil::formatNumberShort(125500));
        $this->assertEquals('1Mn', AppUtil::formatNumberShort(1000000));
    }

    /**
     * Test that number formatting works as expected.
     */
    public function testFormatNumberLong(): void
    {
        $this->assertEquals('2.937Mn', AppUtil::formatNumberLong(2937205));
        $this->assertEquals('2.93Mn', AppUtil::formatNumberLong(2930000));
        $this->assertEquals('2.9Mn', AppUtil::formatNumberLong(2900000));
        $this->assertEquals('2Mn', AppUtil::formatNumberLong(2000000));
        $this->assertEquals('347,598', AppUtil::formatNumberLong(347598));
    }

    /**
     * Test that number formatting works as expected.
     */
    public function testFormatNumber(): void
    {
        $this->assertEquals('2,937,205', AppUtil::formatNumber(2937205));
    }
}
