<?php

namespace Dezinger\SmartCaptcha;

use Dezinger\SmartCaptcha\Exceptions\InvalidConfigurationException;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\IpUtils;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;

/**
 * Class SmartCaptchaBuilder
 * @package Dezinger\SmartCaptcha
 */
class SmartCaptchaBuilder
{
    protected static $allowed_data_attribute = [
        "hl",
        "callback",
    ];

    /**
     * @var int
     */
    const DEFAULT_CURL_TIMEOUT = 10;

    /**
     * @var string
     */
    const DEFAULT_ONLOAD_JS_FUNCTION = 'smartcaptchaOnloadCallback';

    /**
     * @var string
     */
    const DEFAULT_SMARTCAPTCHA_RULE_NAME = 'smartcaptcha';

    /**
     * @var string
     */
    const DEFAULT_SMARTCAPTCHA_FIELD_NAME = 'smartcaptcha-response';

    /**
     * @var string
     */
    const DEFAULT_SMARTCAPTCHA_API_DOMAIN = 'smartcaptcha.yandexcloud.net';

    /**
     * The Site key
     * please visit https://yandex.cloud/en/docs/smartcaptcha/quickstart
     * @var string
     */
    protected $api_site_key;

    /**
     * The Secret key
     * please visit https://yandex.cloud/en/docs/smartcaptcha/quickstart
     * @var string
     */
    protected $api_secret_key;

    /**
     * Whether is true the SmartCaptcha is inactive
     * @var boolean
     */
    protected $skip_by_ip = false;

    /**
     * The API domain (default: retrieved from config file)
     * @var string
     */
    protected $api_domain = '';

    /**
     * The API request URI
     * @var string
     */
    protected $api_url = '';

    /**
     * The URI of the API Javascript file to embed in you pages
     * @var string
     */
    protected $api_js_url = '';

    /**
     * SmartCaptchaBuilder constructor.
     *
     * @param string      $api_site_key
     * @param string      $api_secret_key
     */
    public function __construct(
        string $api_site_key,
        string $api_secret_key
    ) {

        $this->setApiSiteKey($api_site_key);
        $this->setApiSecretKey($api_secret_key);
        $this->setSkipByIp($this->skipByIp());
        $this->setApiDomain();
        $this->setApiUrls();
    }

    /**
     * @param string $api_site_key
     *
     * @return SmartCaptchaBuilder
     */
    public function setApiSiteKey(string $api_site_key): SmartCaptchaBuilder
    {

        $this->api_site_key = $api_site_key;

        return $this;
    }

    /**
     * @param string $api_secret_key
     *
     * @return SmartCaptchaBuilder
     */
    public function setApiSecretKey(string $api_secret_key): SmartCaptchaBuilder
    {

        $this->api_secret_key = $api_secret_key;

        return $this;
    }

    /**
     * @return int
     */
    public function getCurlTimeout(): int
    {
        return config(
            'smartcaptcha.curl_timeout',
            self::DEFAULT_CURL_TIMEOUT
        );
    }

    /**
     * @param bool $skip_by_ip
     *
     * @return SmartCaptchaBuilder
     */
    public function setSkipByIp(bool $skip_by_ip): SmartCaptchaBuilder
    {
        $this->skip_by_ip = $skip_by_ip;

        return $this;
    }

    /**
     * @param null|string $api_domain
     *
     * @return SmartCaptchaBuilder
     */
    public function setApiDomain(?string $api_domain = null): SmartCaptchaBuilder
    {
        $this->api_domain = $api_domain ?? config(
            'smartcaptcha.api_domain',
            self::DEFAULT_SMARTCAPTCHA_API_DOMAIN
        );

        return $this;
    }

    /**
     * @return string
     */
    public function getApiDomain(): string
    {
        return $this->api_domain;
    }

    /**
     * @return SmartCaptchaBuilder
     */
    public function setApiUrls(): SmartCaptchaBuilder
    {
        $this->api_url = 'https://' . $this->api_domain . '/validate';
        $this->api_js_url = 'https://' . $this->api_domain . '/captcha.js';

        return $this;
    }

    /**
     * @return array|mixed
     */
    public function getIpWhitelist()
    {
        $whitelist = config(
            'smartcaptcha.skip_ip',
            []
        );

        if (!is_array($whitelist)) {
            $whitelist = explode(',', $whitelist);
        }

        $whitelist = array_map(function ($item) {

            return trim($item);
        }, $whitelist);

        return $whitelist;
    }

    /**
     * Checks whether the user IP address is among IPs "to be skipped"
     *
     * @return boolean
     */
    public function skipByIp(): bool
    {
        return IpUtils::checkIp(
            request()->ip(),
            $this->getIpWhitelist()
        );
    }

    /**
     * Write script HTML tag in you HTML code
     * Insert before </head> tag
     *
     * @param array|null $configuration
     *
     * @return string
     * @throws \Exception
     */
    public function htmlScriptTagJsApi(?array $configuration = []): string
    {
        $query = [];

        Arr::set($query, 'render', 'onload');
        Arr::set($query, 'onload', self::DEFAULT_ONLOAD_JS_FUNCTION);
        $html = $this->getOnLoadCallback();

        // Create query string
        $query = ($query) ? '?' . http_build_query($query) : "";
        $html .= "<script src=\"" . $this->api_js_url .  $query . "\" async defer></script>";

        return $html;
    }

    /**
     * Call out to SmartCaptcha and process the response
     *
     * @link https://yandex.cloud/en/docs/smartcaptcha/concepts/validation
     *
     * @param string $token
     *
     * @return boolean|array
     */
    public function validate($token)
    {
        if ($this->skip_by_ip) {
            if ($this->returnArray()) {
                return [
                    'skip_by_ip' => true,
                    'status'     => 'ok',
                    'message'    => '',
                    'host'       => '',
                ];
            }

            return true;
        }

        /** @var Response $response */
        $response = Http::asForm()
            ->timeout($this->getCurlTimeout())
            ->post($this->api_url, [
                'secret' => $this->api_secret_key,
                'token' => $token,
                'ip' => request()->getClientIp(),
            ]);

        if ($response->failed()) {
            if ($this->returnArray()) {
                return [
                    'status'  => 'failed',
                    'message' => 'Http response empty',
                ];
            }

            return false;
        }

        if ($this->returnArray()) {
            return $response->json();
        }

        return $response->json('status') === 'ok';
    }

    /**
     * @return string
     */
    public function getApiSiteKey(): string
    {

        return $this->api_site_key;
    }

    /**
     * @return string
     */
    public function getApiSecretKey(): string
    {

        return $this->api_secret_key;
    }

    /**
     * @return bool
     */
    protected function returnArray(): bool
    {

        return false;
    }

    /**
     * Write SmartCaptcha HTML tag in your FORM
     * Insert before </form> tag
     *
     * @param null|array $attributes
     * @return string
     * @throws InvalidConfigurationException
     */
    public function htmlFormSnippet(?array $attributes = []): string
    {
        $data_attributes = [];

        $config_data_attributes = array_merge(
            $this->getTagAttributes(),
            self::cleanAttributes($attributes)
        );

        ksort($config_data_attributes);
        foreach ($config_data_attributes as $k => $v) {
            if ($v) {
                $data_attributes[] = 'data-' . $k . '="' . $v . '"';
            }
        }

        $html = '<div class="smartcaptcha" ' . implode(" ", $data_attributes) . ' id="smartcaptcha-element"></div>';

        return $html;
    }

    /**
     * @return array
     * @throws InvalidConfigurationException
     */
    public function getTagAttributes(): array
    {
        $tag_attributes = [
            'sitekey' => $this->api_site_key
        ];

        $tag_attributes = array_merge(
            $tag_attributes,
            config('smartcaptcha.tag_attributes', [])
        );

        if (Arr::get($tag_attributes, 'callback') === self::DEFAULT_ONLOAD_JS_FUNCTION) {
            throw new InvalidConfigurationException(
                'Property "callback" ("data-callback") must be different from "' . self::DEFAULT_ONLOAD_JS_FUNCTION . '"'
            );
        }

        return $tag_attributes;
    }

    /**
     * Compare given attributes with allowed attributes
     *
     * @param array|null $attributes
     * @return array
     */
    public static function cleanAttributes(?array $attributes = []): array
    {
        return array_filter($attributes, function ($k) {
            return in_array($k, self::$allowed_data_attribute);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * @return string
     * @throws InvalidConfigurationException
     */
    public function getOnLoadCallback(): string
    {
        $attributes = $this->getTagAttributes();
        return "<script>var " . self::DEFAULT_ONLOAD_JS_FUNCTION . " = function() {smartCaptcha.render('smartcaptcha-element', " . json_encode($attributes) . ");};</script>";
    }
}
