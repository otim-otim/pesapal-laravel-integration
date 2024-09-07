<?php

namespace OtimOtim\PesapalIntegrationPackage\Tests;

use Orchestra\Testbench\TestCase;
use OtimOtim\PesapalIntegrationPackage\PesapalIntegrationPackageServiceProvider;

class PackageTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            PesapalIntegrationPackageServiceProvider::class,
        ];
    }

    /** @test */
    public function it_can_run_artisan_commands()
    {
        $this->artisan('vendor:publish', ['--tag' => 'config'])
             ->assertExitCode(0);
    }
}
