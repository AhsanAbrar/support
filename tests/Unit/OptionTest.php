<?php

namespace AhsanDev\Support\Tests\Unit;

use AhsanDev\Support\Contracts\Option as OptionContract;
use AhsanDev\Support\Option;
use AhsanDev\Support\Tests\TestCase;

class OptionTest extends TestCase
{
    public function test_instance_of_option_contract()
    {
        $option = new Option;

        $this->assertInstanceOf(OptionContract::class, $option);
    }
}
