<?php

namespace Dezinger\SmartCaptcha\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class SmartCaptcha
 * @package Dezinger\SmartCaptcha\Facades
 *
 * @method static string htmlScriptTagJsApi(?array $config = [])
 * @method static string htmlFormSnippet()
 */
class SmartCaptcha extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {

        return 'smartcaptcha';
    }
}
