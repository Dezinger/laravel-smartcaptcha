<?php

/**
 * To configure correctly please visit https://yandex.cloud/en/docs/smartcaptcha/quickstart
 */
return array(

    /**
     *
     * The site key
     *
     */
    'api_site_key'                 => env('SMARTCAPTCHA_SITE_KEY', ''),

    /**
     *
     * The secret key
     *
     */
    'api_secret_key'               => env('SMARTCAPTCHA_SECRET_KEY', ''),

    /**
     *
     * The curl timout in seconds to validate a smartcaptcha token
     *
     */
    'curl_timeout'                 => 10,

    /**
     *
     * IP addresses for which validation will be skipped
     * IP/CIDR netmask eg. 127.0.0.0/24, also 127.0.0.1 is accepted and /32 assumed
     *
     */
    'skip_ip'                      => env('SMARTCAPTCHA_SKIP_IP', array()),

    /**
     *
     * The name of the parameter used to send SmartCaptcha token to verify route
     *
     */
    'default_token_parameter_name' => 'token',

    /**
     *
     * The default Yandex SmartCaptcha language code
     *
     */
    'default_language'             => null,

    /**
     *
     * Set API domain.
     *
     */
    'api_domain'                   => 'smartcaptcha.yandexcloud.net',

    /**
     *
     * Set `true` when the error message must be null
     * Default false
     *
     */
    'empty_message' => false,

    /**
     *
     * Set either the error message or the errom message translation key
     * Default 'validation.smartcaptcha'
     *
     */
    'error_message_key' => 'validation.smartcaptcha',

    /**
     *
     * smartcaptcha tag attributes and smartCaptcha.render parameters
     * @see https://yandex.cloud/ru/docs/smartcaptcha/concepts/widget-methods
     */
    'tag_attributes'               => array(

        /**
         * The name of your callback function, executed when the user submits a successful response.
         * The smartcaptcha-response token is passed to your callback.
         * DO NOT SET "smartcaptchaOnloadCallback"
         */
        'callback'         => null,
    )
);
