# Custom Checks

1. Create a class implementing `StackHealthCheckContract`.
2. Return `StackHealthItemResult[]`.
3. Add class to a `stack-health.sections` list.
4. Add translation keys for label/messages.
5. Add unit tests for success + failure + redaction.

## Example

```php
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Contracts\StackHealthCheckContract;
use JustGetSchwifty\LaravelStackHealth\Services\StackHealth\Data\StackHealthItemResult;

final class QueueConnectionHealthCheck implements StackHealthCheckContract
{
    public static function id(): string
    {
        return 'queue_connection_health';
    }

    public function run(): array
    {
        $ok = (string) config('queue.default', '') !== '';

        return [
            new StackHealthItemResult(
                'Queue connection',
                $ok,
                $ok ? 'Queue is configured' : 'Queue is missing'
            ),
        ];
    }
}
```
