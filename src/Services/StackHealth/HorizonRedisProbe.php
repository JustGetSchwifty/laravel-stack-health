<?php

namespace JustGetSchwifty\LaravelStackHealth\Services\StackHealth;

use Illuminate\Support\Facades\Redis;
use Throwable;

class HorizonRedisProbe
{
    /**
     * Detect Horizon activity via sorted sets Horizon maintains (O(1) per command, one pipeline round-trip).
     *
     * @return array{ok: bool, masters: int, supervisors: int, error: string|null}
     */
    public function hasHorizonIndices(): array
    {
        try {
            $connectionName = (string) config('stack-health.horizon_redis_connection', 'horizon');
            $connection = Redis::connection($connectionName);

            $results = $connection->pipeline(function ($pipe) {
                $pipe->zcard('masters');
                $pipe->zcard('supervisors');
            });

            if (! is_array($results)) {
                $masters = (int) $connection->zcard('masters');
                $supervisors = (int) $connection->zcard('supervisors');
            } else {
                $masters = (int) ($results[0] ?? 0);
                $supervisors = (int) ($results[1] ?? 0);
            }

            $ok = $masters > 0 || $supervisors > 0;

            return [
                'ok' => $ok,
                'masters' => $masters,
                'supervisors' => $supervisors,
                'error' => null,
            ];
        } catch (Throwable $e) {
            return [
                'ok' => false,
                'masters' => 0,
                'supervisors' => 0,
                'error' => $e->getMessage(),
            ];
        }
    }
}
