<?php

namespace JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Checks;

use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Contracts\StackHealthCheckContract;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Data\StackHealthItemResult;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\StackHealthMessageRedactor;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\RequestOptions;
use Throwable;

/**
 * Optional outbound HEAD request to verify egress/DNS/TLS; disabled via config when air-gapped.
 *
 * Uses an injected {@see ClientInterface} so tests can substitute a mock without touching the network.
 */
final class OutboundHttpCheck implements StackHealthCheckContract
{
    public function __construct(
        private StackHealthMessageRedactor $redactor,
        private ClientInterface $httpClient,
    ) {}

    public static function id(): string
    {
        return 'outbound_http';
    }

    /**
     * @return list<StackHealthItemResult>
     */
    public function run(): array
    {
        $name = __('stack-health::stack-health.checks.outbound_guzzle');

        if (! config('stack-health.outbound_http')) {
            return [
                new StackHealthItemResult(
                    $name,
                    null,
                    __('stack-health::stack-health.messages.outbound_disabled'),
                ),
            ];
        }

        $url = (string) config('stack-health.outbound_http_url', 'https://www.google.com');
        $timeout = (float) config('stack-health.outbound_http_timeout', 5);

        if ($url === '' || filter_var($url, FILTER_VALIDATE_URL) === false) {
            return [
                new StackHealthItemResult(
                    $name,
                    false,
                    __('stack-health::stack-health.messages.outbound_invalid_url'),
                ),
            ];
        }

        try {
            $connectTimeout = min(3.0, max(0.5, $timeout));
            $response = $this->httpClient->request('HEAD', $url, [
                RequestOptions::TIMEOUT => $timeout,
                RequestOptions::CONNECT_TIMEOUT => $connectTimeout,
                RequestOptions::HTTP_ERRORS => false,
                RequestOptions::ALLOW_REDIRECTS => ['max' => 5],
                RequestOptions::HEADERS => [
                    'User-Agent' => 'StackHealthReporter/1.0',
                ],
            ]);
            $code = $response->getStatusCode();
            $ok = $code >= 200 && $code < 400;

            return [
                new StackHealthItemResult(
                    $name,
                    $ok,
                    $this->redactor->shouldRedact()
                        ? __('stack-health::stack-health.messages.outbound_head_redacted', ['code' => $code])
                        : __('stack-health::stack-health.messages.outbound_head', ['url' => $url, 'code' => $code]),
                ),
            ];
        } catch (Throwable $e) {
            return [
                new StackHealthItemResult(
                    $name,
                    false,
                    $this->redactor->exceptionMessageForUi($e, 'stack-health::stack-health.messages.error_outbound_http'),
                ),
            ];
        }
    }
}
