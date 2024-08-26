<?php

namespace OtimOtim\PesapalIntegrationPackage;

use Illuminate\Support\ServiceProvider;

class PesapalIntegrationPackageServiceProvider extends ServiceProvider
{
  public function register()
  {
    $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'PesapalIntegrationPackage');
  }

  public function boot()
  {
    if ($this->app->runningInConsole()) {

        $this->publishes([
          __DIR__.'/../config/config.php' => config_path('PesapalIntegrationPackage.php'),
        ], 'config');
        // php artisan vendor:publish --provider="OtimOtim\PesapalIntegrationPackage\PesapalIntegrationPackageServiceProvider" --tag="config"
    
      }
  }
}