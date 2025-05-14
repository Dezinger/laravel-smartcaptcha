<?php

use Dezinger\SmartCaptcha\Facades\SmartCaptcha;

if (!function_exists('smartcaptcha')) {
    /**
     * @return Dezinger\SmartCaptcha\SmartCaptchaBuilder
     */
    function smartcaptcha(): \Dezinger\SmartCaptcha\SmartCaptchaBuilder
    {

        return app('smartcaptcha');
    }
}

/**
 * call SmartCaptcha::htmlScriptTagJsApi()
 * Write script HTML tag in you HTML code
 * Insert before </head> tag
 *
 * @param $config
 */
if (!function_exists('htmlScriptTagJsApi')) {

    /**
     * @param array|null $config
     *
     * @return string
     */
    function htmlScriptTagJsApi(?array $config = []): string
    {

        return SmartCaptcha::htmlScriptTagJsApi($config);
    }
}

/**
 * call SmartCaptcha::htmlFormSnippet()
 * Write SmartCaptcha HTML tag in your FORM
 * Insert before </form> tag
 */
if (!function_exists('htmlFormSnippet')) {

    /**
     * @param null|array $attributes
     * @return string
     */
    function htmlFormSnippet(?array $attributes = []): string
    {

        return SmartCaptcha::htmlFormSnippet($attributes);
    }
}

/**
 * return SmartCaptchaBuilder::DEFAULT_SMARTCAPTCHA_RULE_NAME value ("smartcaptcha")
 */
if (!function_exists('smartcaptchaRuleName')) {

    /**
     * @return string
     */
    function smartcaptchaRuleName(): string
    {
        return \Dezinger\SmartCaptcha\SmartCaptchaBuilder::DEFAULT_SMARTCAPTCHA_RULE_NAME;
    }
}

/**
 * return SmartCaptchaBuilder::DEFAULT_SMARTCAPTCHA_FIELD_NAME value "smartcaptcha-response"
 * Use V2 (checkbox and invisible)
 */
if (!function_exists('smartcaptchaFieldName')) {

    /**
     * @return string
     */
    function smartcaptchaFieldName(): string
    {

        return \Dezinger\SmartCaptcha\SmartCaptchaBuilder::DEFAULT_SMARTCAPTCHA_FIELD_NAME;
    }
}
