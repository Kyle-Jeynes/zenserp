<?php

namespace Zenserp\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class ZenserpServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/zenserp.php', 'zenserp');
    }

    public function boot(): void
    {
        RateLimiter::for(config('zenserp.rate_limit.limiter', 'zenserp'), $this->rateLimiter(...));

        $this->loadRoutesFrom(__DIR__.'/../Http/routes.php');
        $this->loadMigrationsFrom(dirname(__DIR__, 2).'/database/migrations');
    }

    private function rateLimiter(Request $request): Limit
    {
        ['max_attempts' => $maxAttempts, 'decay_seconds' => $decaySeconds] = config('zenserp.rate_limit', [
            'max_attempts' => 60,
            'decay_seconds' => 60,
        ]);

        $decayMinutes = max(1, (int) ceil(((int) $decaySeconds) / 60));

        return Limit::perMinutes($decayMinutes, max(1, (int) $maxAttempts))
            ->by((string) $request->ip());
    }
}
