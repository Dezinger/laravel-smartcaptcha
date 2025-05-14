<?php

use Dezinger\SmartCaptcha\Facades\SmartCaptcha;
use Dezinger\SmartCaptcha\SmartCaptchaBuilder;

if (!function_exists('smartcaptcha')) {
    /**
     * @return SmartCaptchaBuilder
     */
    function smartcaptcha(): SmartCaptchaBuilder
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
if (!function_exists('smartcaptchaHtmlScriptTagJsApi')) {

    /**
     * @param array|null $config
     *
     * @return string
     */
    function smartcaptchaHtmlScriptTagJsApi(?array $config = []): string
    {
        return SmartCaptcha::htmlScriptTagJsApi($config);
    }
}

/**
 * call SmartCaptcha::htmlFormSnippet()
 * Write SmartCaptcha HTML tag in your FORM
 * Insert before </form> tag
 */
if (!function_exists('smartcaptchaHtmlFormSnippet')) {

    /**
     * @param null|array $attributes
     * @return string
     */
    function smartcaptchaHtmlFormSnippet(?array $attributes = []): string
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
        return SmartCaptchaBuilder::DEFAULT_SMARTCAPTCHA_RULE_NAME;
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
        return SmartCaptchaBuilder::DEFAULT_SMARTCAPTCHA_FIELD_NAME;
    }
}
