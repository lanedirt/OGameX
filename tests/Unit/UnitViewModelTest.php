<?php

namespace Tests\Unit;

use OGame\ViewModels\UnitViewModel;
use Tests\UnitTestCase;

class UnitViewModelTest extends UnitTestCase
{
    public function test_formatted_amount(): void
    {
        // Create an instance of UnitViewModel
        $unitViewModel = new UnitViewModel();

        // Set the amount
        $unitViewModel->amount = 12345;

        // Assert that the formatted amount is correct
        $this->assertEquals('12K', $unitViewModel->getFormatted());
    }

    public function test_formatted_full_amount(): void
    {
        // Create an instance of UnitViewModel
        $unitViewModel = new UnitViewModel();

        // Set the amount
        $unitViewModel->amount = 12345;

        // Assert that the formatted full amount is correct
        $this->assertEquals('12,345', $unitViewModel->getFormattedFull());
    }

    public function test_formatted_long_amount(): void
    {
        // Create an instance of UnitViewModel
        $unitViewModel = new UnitViewModel();

        // Set the amount
        $unitViewModel->amount = 12345;

        // Assert that the formatted long amount is correct
        $this->assertEquals('12,345', $unitViewModel->getFormattedLong());
    }
}
