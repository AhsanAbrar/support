<?php

use AhsanDev\Support\Contracts\Option as OptionContract;
use AhsanDev\Support\Option;

it('should be an instance of OptionContract', function () {
    expect(new Option)->toBeInstanceOf(OptionContract::class);
});
