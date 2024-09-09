<?php

namespace OtimOtim\PesapalIntegrationPackage;

use Illuminate\Support\ServiceProvider;
use OtimOtim\PesapalIntegrationPackage\Services\PesapalService;

class PesapalIntegrationPackageServiceProvider extends ServiceProvider
{
  public function register()
  {
    $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'PesapalIntegrationPackage');

    $this->app->bind('pesapal', function ($app) {
      return new PesapalService();
    });
  }

  public function boot()
  {
    if ($this->app->runningInConsole()) {

        $this->publishes([
          __DIR__.'/../config/config.php' => config_path('PesapalIntegrationPackage.php'),
        ], 'config');
        // php artisan vendor:publish --provider="OtimOtim\PesapalIntegrationPackage\PesapalIntegrationPackageServiceProvider" --tag="config"

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    
      }

      $this->ensureConfigIsSet();
  }

  /**
     * Ensure all required config variables are set.
     *
     * @throws \Exception
     */
    protected function ensureConfigIsSet()
    {
        $requiredConfigKeys = [
            'PesapalIntegrationPackage.CONSUMER_KEY',
            'PesapalIntegrationPackage.CONSUMER_SECRET',
            'PesapalIntegrationPackage.NOTIFICATION_ID',
            'PesapalIntegrationPackage.NOTIFICATION_URL',
            'PesapalIntegrationPackage.CALLBACK_URL',
        ];

        // Check environment-specific URL
        $environment = config('APP_ENV');
        if ($environment == 'production') {
            $requiredConfigKeys[] = 'PesapalIntegrationPackage.LIVE_URL';
        } else {
            $requiredConfigKeys[] = 'PesapalIntegrationPackage.SAND_BOX_URL';
        }




        foreach ($requiredConfigKeys as $key) {
            if (!config($key)) {
                throw new \Exception("Pesapal Integration Package is not configured. Missing: {$key}");
            }
        }
    }
}