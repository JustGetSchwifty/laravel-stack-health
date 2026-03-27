<?php

namespace JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Checks;

use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Contracts\StackHealthCheckContract;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Data\StackHealthItemResult;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\StackHealthMessageRedactor;
use Throwable;

/**
 * Inspects mailer config and, for SMTP, opens a TCP/TLS socket only — no email is sent.
 *
 * API-style transports are reported as informational (null) because socket probes do not apply.
 */
final class MailTransportCheck implements StackHealthCheckContract
{
    public function __construct(
        private StackHealthMessageRedactor $redactor,
    ) {}

    public static function id(): string
    {
        return 'mail_transport';
    }

    /**
     * @return list<StackHealthItemResult>
     */
    public function run(): array
    {
        $name = __('stack-health::stack-health.checks.mail_transport');
        $mailerName = (string) config('mail.default');
        $cfg = config("mail.mailers.{$mailerName}");

        if (! is_array($cfg)) {
            return [
                new StackHealthItemResult(
                    $name,
                    false,
                    __('stack-health::stack-health.messages.mail_mailer_missing', ['mailer' => $mailerName]),
                ),
            ];
        }

        $transport = $cfg['transport'] ?? '';

        if (in_array($transport, ['log', 'array'], true)) {
            if (app()->environment('local')) {
                return [
                    new StackHealthItemResult(
                        $name,
                        true,
                        __('stack-health::stack-health.messages.mail_mock_local_ok', [
                            'mailer' => $mailerName,
                            'transport' => $transport,
                        ]),
                    ),
                ];
            }

            return [
                new StackHealthItemResult(
                    $name,
                    null,
                    __('stack-health::stack-health.messages.mail_mock_warn', [
                        'mailer' => $mailerName,
                        'transport' => $transport,
                    ]),
                ),
            ];
        }

        if (in_array($transport, ['failover', 'roundrobin'], true)) {
            return [
                new StackHealthItemResult(
                    $name,
                    null,
                    __('stack-health::stack-health.messages.mail_complex_transport', ['transport' => $transport]),
                ),
            ];
        }

        if ($transport === 'sendmail') {
            $path = (string) ($cfg['path'] ?? config('mail.mailers.sendmail.path', ''));
            $binary = $path !== '' ? preg_split('/\s+/', trim($path))[0] : '';

            if ($binary === '' || ! @is_executable($binary)) {
                return [
                    new StackHealthItemResult(
                        $name,
                        false,
                        __('stack-health::stack-health.messages.mail_sendmail_bad', [
                            'path' => $path ?: __('stack-health::stack-health.messages.mail_empty_path_placeholder'),
                        ]),
                    ),
                ];
            }

            return [
                new StackHealthItemResult(
                    $name,
                    true,
                    __('stack-health::stack-health.messages.mail_sendmail_ok', ['binary' => $binary]),
                ),
            ];
        }

        if ($transport !== 'smtp') {
            return [
                new StackHealthItemResult(
                    $name,
                    null,
                    __('stack-health::stack-health.messages.mail_api_transport', ['transport' => $transport]),
                ),
            ];
        }

        $host = $cfg['host'] ?? '127.0.0.1';
        $port = (int) ($cfg['port'] ?? 2525);
        $scheme = $cfg['scheme'] ?? null;

        if (! empty($cfg['url']) && is_string($cfg['url'])) {
            $parsed = parse_url($cfg['url']);
            if (is_array($parsed) && ! empty($parsed['host'])) {
                $host = $parsed['host'];
                if (isset($parsed['port'])) {
                    $port = (int) $parsed['port'];
                }
                $scheme = $parsed['scheme'] ?? $scheme;
            }
        }

        $useSsl = ($scheme === 'smtps') || $port === 465;
        $remote = $useSsl ? "ssl://{$host}:{$port}" : "tcp://{$host}:{$port}";
        $timeout = (float) config('stack-health.smtp_probe_timeout', 8);
        $verifySsl = (bool) config('stack-health.smtp_probe_verify_ssl', true);

        $context = [];
        if ($useSsl) {
            $context['ssl'] = [
                'verify_peer' => $verifySsl,
                'verify_peer_name' => $verifySsl,
                'allow_self_signed' => ! $verifySsl,
            ];
        }

        $ctx = $context !== [] ? stream_context_create($context) : null;
        $errno = 0;
        $errstr = '';

        try {
            $fp = @stream_socket_client(
                $remote,
                $errno,
                $errstr,
                $timeout,
                STREAM_CLIENT_CONNECT,
                $ctx
            );
        } catch (Throwable $e) {
            report($e);

            return [
                new StackHealthItemResult(
                    $name,
                    false,
                    $this->redactor->shouldRedact()
                        ? __('stack-health::stack-health.messages.mail_smtp_failed_redacted')
                        : __('stack-health::stack-health.messages.mail_smtp_failed', [
                            'target' => $remote,
                            'error' => $e->getMessage(),
                        ]),
                ),
            ];
        }

        if ($fp === false) {
            $errDetail = trim($errstr !== '' ? $errstr : ('errno '.$errno));

            return [
                new StackHealthItemResult(
                    $name,
                    false,
                    $this->redactor->shouldRedact()
                        ? __('stack-health::stack-health.messages.mail_smtp_failed_redacted')
                        : __('stack-health::stack-health.messages.mail_smtp_failed', [
                            'target' => $remote,
                            'error' => $errDetail,
                        ]),
                ),
            ];
        }

        fclose($fp);

        return [
            new StackHealthItemResult(
                $name,
                true,
                $this->redactor->shouldRedact()
                    ? __('stack-health::stack-health.messages.mail_smtp_ok_redacted', [
                        'encryption' => $useSsl ? 'TLS' : 'TCP',
                        'mailer' => $mailerName,
                    ])
                    : __('stack-health::stack-health.messages.mail_smtp_ok', [
                        'target' => $remote,
                        'mailer' => $mailerName,
                    ]),
            ),
        ];
    }
}
