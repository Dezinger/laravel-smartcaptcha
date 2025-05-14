<?php

namespace Dezinger\SmartCaptcha;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

/**
 * Class SmartCaptchaServiceProvider
 * @package Dezinger\SmartCaptcha
 */
class SmartCaptchaServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     *
     */
    public function boot()
    {
        $this->addValidationRule();
        $this->registerRoutes();
        $this->publishes([
            __DIR__ . '/../config/smartcaptcha.php' => config_path('smartcaptcha.php'),
        ], 'config');
    }

    /**
     * Extends Validator to include a smartcaptcha type
     */
    public function addValidationRule()
    {
        $message = null;

        if (!config('smartcaptcha.empty_message')) {
            $message = trans(config('smartcaptcha.error_message_key'));
        }

        Validator::extendImplicit(smartcaptchaRuleName(), function ($attribute, $value) {
            return app('smartcaptcha')->validate($value);
        }, $message);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/smartcaptcha.php',
            'smartcaptcha'
        );

        $this->registerSmartCaptchaBuilder();
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array
    {
        return ['smartcaptcha'];
    }

    /**
     * @return SmartCaptchaServiceProvider
     */
    protected function registerRoutes(): SmartCaptchaServiceProvider
    {
        return $this;
    }

    /**
     * Register the HTML builder instance.
     *
     * @return void
     */
    protected function registerSmartCaptchaBuilder()
    {
        $this->app->singleton('smartcaptcha', function ($app) {
            return new SmartCaptchaBuilder(
                config('smartcaptcha.api_site_key'),
                config('smartcaptcha.api_secret_key')
            );
        });
    }
}
