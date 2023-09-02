<?php

namespace Tests;

use AhsanDev\Support\SupportServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app)
    {
        return [SupportServiceProvider::class];
    }
}
