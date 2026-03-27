<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('stack-health::stack-health.title', ['app' => config('app.name')]) }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700|jetbrains-mono:400,500" rel="stylesheet">
    <style>
        :root {
            --bg: #0c0f14;
            --panel: #151a22;
            --border: #2a3344;
            --text: #e8ecf1;
            --muted: #8b98a8;
            --ok: #3dd68c;
            --fail: #f56565;
            --warn: #ecc94b;
            --accent: #63b3ed;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Instrument Sans", system-ui, sans-serif;
            background: var(--bg);
            color: var(--text);
            background-image:
                radial-gradient(ellipse 120% 80% at 100% -20%, rgba(99, 179, 237, 0.12), transparent),
                radial-gradient(ellipse 80% 60% at -10% 100%, rgba(61, 214, 140, 0.08), transparent);
        }
        .wrap {
            max-width: 960px;
            margin: 0 auto;
            padding: 2.5rem 1.25rem 4rem;
        }
        header.page-header {
            margin-bottom: 2rem;
            border-left: 4px solid var(--accent);
            padding-left: 1.25rem;
            display: flex;
            flex-wrap: wrap;
            align-items: flex-start;
            justify-content: space-between;
            gap: 0.85rem 1.25rem;
        }
        .page-header__main { flex: 1; min-width: 0; }
        h1 {
            font-size: 1.75rem;
            font-weight: 700;
            margin: 0;
            letter-spacing: -0.02em;
        }
        .env-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.38rem 0.85rem 0.38rem 0.65rem;
            border-radius: 999px;
            font-family: "JetBrains Mono", ui-monospace, monospace;
            font-size: 0.62rem;
            font-weight: 600;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: #f0e6a8;
            border: 1px solid rgba(236, 201, 75, 0.45);
            background: linear-gradient(145deg, rgba(55, 48, 22, 0.95) 0%, rgba(28, 32, 40, 0.98) 100%);
            box-shadow:
                0 0 0 1px rgba(0, 0, 0, 0.35),
                0 4px 20px -4px rgba(236, 201, 75, 0.35),
                inset 0 1px 0 rgba(255, 255, 255, 0.06);
            flex-shrink: 0;
        }
        .env-badge__pulse {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--warn);
            box-shadow: 0 0 10px rgba(236, 201, 75, 0.9);
            animation: env-badge-pulse 2s ease-in-out infinite;
        }
        @keyframes env-badge-pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.65; transform: scale(0.92); }
        }
        .section {
            margin-bottom: 2rem;
        }
        .section h2 {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: var(--muted);
            margin: 0 0 0.75rem;
            font-weight: 600;
        }
        .grid {
            display: grid;
            gap: 0.65rem;
        }
        @media (min-width: 640px) {
            .grid { grid-template-columns: repeat(2, 1fr); }
        }
        .card {
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 0.85rem 1rem;
            display: flex;
            gap: 0.75rem;
            align-items: flex-start;
        }
        .dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-top: 0.35rem;
            flex-shrink: 0;
        }
        .dot-ok { background: var(--ok); box-shadow: 0 0 12px rgba(61, 214, 140, 0.45); }
        .dot-fail { background: var(--fail); box-shadow: 0 0 12px rgba(245, 101, 101, 0.4); }
        .dot-warn { background: var(--warn); opacity: 0.9; }
        .card-body { min-width: 0; }
        .name {
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 0.25rem;
        }
        .msg {
            font-family: "JetBrains Mono", ui-monospace, monospace;
            font-size: 0.72rem;
            color: var(--muted);
            line-height: 1.45;
            word-break: break-word;
        }
        footer {
            margin-top: 3rem;
            font-size: 0.8rem;
            color: var(--muted);
        }

        .summary {
            border-radius: 14px;
            padding: 1.25rem 1.35rem;
            margin-bottom: 2rem;
            border: 1px solid var(--border);
            background: var(--panel);
            position: relative;
            overflow: hidden;
        }
        .summary::before {
            content: "";
            position: absolute;
            inset: 0;
            opacity: 0.12;
            pointer-events: none;
        }
        .summary--ok {
            border-color: rgba(61, 214, 140, 0.45);
            box-shadow: 0 0 0 1px rgba(61, 214, 140, 0.15), 0 12px 40px -20px rgba(61, 214, 140, 0.35);
        }
        .summary--ok::before {
            background: radial-gradient(ellipse 80% 120% at 0% 50%, var(--ok), transparent 65%);
        }
        .summary--warn {
            border-color: rgba(236, 201, 75, 0.5);
            box-shadow: 0 0 0 1px rgba(236, 201, 75, 0.12), 0 12px 40px -20px rgba(236, 201, 75, 0.25);
        }
        .summary--warn::before {
            background: radial-gradient(ellipse 80% 120% at 0% 50%, var(--warn), transparent 65%);
        }
        .summary--fail {
            border-color: rgba(245, 101, 101, 0.55);
            box-shadow: 0 0 0 1px rgba(245, 101, 101, 0.15), 0 12px 40px -20px rgba(245, 101, 101, 0.35);
        }
        .summary--fail::before {
            background: radial-gradient(ellipse 80% 120% at 0% 50%, var(--fail), transparent 65%);
        }
        .summary__inner {
            position: relative;
            display: flex;
            flex-wrap: wrap;
            align-items: flex-start;
            gap: 1rem 1.5rem;
        }
        .summary__mark {
            width: 3rem;
            height: 3rem;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.45rem;
            font-weight: 700;
            flex-shrink: 0;
        }
        .summary--ok .summary__mark {
            background: rgba(61, 214, 140, 0.18);
            color: var(--ok);
        }
        .summary--warn .summary__mark {
            background: rgba(236, 201, 75, 0.18);
            color: var(--warn);
        }
        .summary--fail .summary__mark {
            background: rgba(245, 101, 101, 0.18);
            color: var(--fail);
        }
        .summary__text { flex: 1; min-width: 200px; }
        .summary__title {
            font-size: 1.2rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            margin: 0 0 0.35rem;
        }
        .summary__line {
            margin: 0;
            color: var(--muted);
            font-size: 0.95rem;
            line-height: 1.5;
        }
        .summary__line strong:not(.c-fail):not(.c-warn) {
            color: var(--text);
            font-weight: 600;
        }
        .summary__line .c-fail { color: var(--fail); font-weight: 600; }
        .summary__line .c-warn { color: var(--warn); font-weight: 600; }
        .summary__counts {
            font-family: "JetBrains Mono", ui-monospace, monospace;
            font-size: 0.8rem;
            color: var(--muted);
            margin-top: 0.5rem;
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem 1rem;
        }
        .summary__counts span { white-space: nowrap; }
        .summary__counts .c-ok { color: var(--ok); }
        .summary__counts .c-fail { color: var(--fail); }
        .summary__counts .c-warn { color: var(--warn); }
        .summary__bar {
            width: 100%;
            flex-basis: 100%;
            height: 8px;
            border-radius: 999px;
            display: flex;
            overflow: hidden;
            background: rgba(42, 51, 68, 0.6);
            margin-top: 0.25rem;
        }
        .summary__bar > span {
            height: 100%;
            min-width: 0;
            transition: width 0.35s ease;
        }
        .summary__bar-pass { background: linear-gradient(90deg, #2ea86a, var(--ok)); }
        .summary__bar-warn { background: linear-gradient(90deg, #c9a227, var(--warn)); }
        .summary__bar-fail { background: linear-gradient(90deg, #c53030, var(--fail)); }
    </style>
</head>
<body>
    <div class="wrap">
        <header class="page-header">
            <div class="page-header__main">
                <h1>{{ __('stack-health::stack-health.heading') }}</h1>
            </div>
            @env('local')
                <span
                    class="env-badge"
                    role="status"
                    aria-label="{{ __('stack-health::stack-health.local_badge_aria') }}"
                    title="{{ __('stack-health::stack-health.local_badge_aria') }}"
                >
                    <span class="env-badge__pulse" aria-hidden="true"></span>
                    {{ __('stack-health::stack-health.local_badge') }}
                </span>
            @endenv
        </header>

        <section
            class="summary summary--{{ $summary['state'] }}"
            data-summary-state="{{ $summary['state'] }}"
            role="region"
            aria-label="{{ __('stack-health::stack-health.summary.region_aria') }}"
        >
            <div class="summary__inner">
                <div class="summary__mark" aria-hidden="true">{{ $summary['mark'] }}</div>
                <div class="summary__text">
                    <h2 class="summary__title">{{ $summary['headline'] }}</h2>
                    <p class="summary__line">
                        @if ($summary['total'] === 0)
                            {{ __('stack-health::stack-health.summary.no_items') }}
                        @else
                            <strong>{{ $summary['pass'] }}</strong> {{ __('stack-health::stack-health.summary.of') }} <strong>{{ $summary['total'] }}</strong> {{ __('stack-health::stack-health.summary.checks_passed') }}
                            @if ($summary['fail'] > 0 || $summary['warn'] > 0)
                                ·
                            @endif
                            @if ($summary['fail'] > 0)
                                <strong class="c-fail">@choice('stack-health::stack-health.summary.failures', $summary['fail'])</strong>
                            @endif
                            @if ($summary['fail'] > 0 && $summary['warn'] > 0)
                                ·
                            @endif
                            @if ($summary['warn'] > 0)
                                <strong class="c-warn">@choice('stack-health::stack-health.summary.warnings', $summary['warn'])</strong>
                            @endif
                        @endif
                    </p>
                    @if ($summary['total'] > 0)
                        <div class="summary__counts" aria-hidden="true">
                            <span class="c-ok">{{ __('stack-health::stack-health.summary.count_ok', ['count' => $summary['pass']]) }}</span>
                            @if ($summary['warn'] > 0)<span class="c-warn">{{ __('stack-health::stack-health.summary.count_warn', ['count' => $summary['warn']]) }}</span>@endif
                            @if ($summary['fail'] > 0)<span class="c-fail">{{ __('stack-health::stack-health.summary.count_fail', ['count' => $summary['fail']]) }}</span>@endif
                        </div>
                    @endif
                </div>
                @if ($summary['total'] > 0)
                    <div class="summary__bar" title="{{ __('stack-health::stack-health.summary.bar_title') }}">
                        @if ($summary['pct_pass'] > 0)<span class="summary__bar-pass" style="width: {{ $summary['pct_pass'] }}%;"></span>@endif
                        @if ($summary['pct_warn'] > 0)<span class="summary__bar-warn" style="width: {{ $summary['pct_warn'] }}%;"></span>@endif
                        @if ($summary['pct_fail'] > 0)<span class="summary__bar-fail" style="width: {{ $summary['pct_fail'] }}%;"></span>@endif
                    </div>
                @endif
            </div>
        </section>

        @foreach ($sections as $section)
            <section class="section">
                <h2>{{ $section['title'] }}</h2>
                <div class="grid">
                    @foreach ($section['items'] as $item)
                        @php
                            $ok = $item['ok'];
                            $dot = $ok === true ? 'ok' : ($ok === false ? 'fail' : 'warn');
                            $ariaOutcome = $dot === 'ok'
                                ? __('stack-health::stack-health.aria.check_ok')
                                : ($dot === 'fail'
                                    ? __('stack-health::stack-health.aria.check_fail')
                                    : __('stack-health::stack-health.aria.check_warn'));
                        @endphp
                        <article class="card" role="status" aria-label="{{ $item['name'] }} — {{ $ariaOutcome }}">
                            <span class="dot dot-{{ $dot }}" aria-hidden="true"></span>
                            <div class="card-body">
                                <div class="name">{{ $item['name'] }}</div>
                                <div class="msg">{{ $item['message'] }}</div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        @endforeach

        <footer>
            <span data-stack-footer>{{ __('stack-health::stack-health.footer', [
                'app' => config('app.name'),
                'php_version' => PHP_VERSION,
                'timestamp' => now()->timezone(config('app.timezone'))->toIso8601String(),
            ]) }}</span>
        </footer>
    </div>
</body>
</html>
