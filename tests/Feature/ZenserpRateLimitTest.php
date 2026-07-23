<?php

use Illuminate\Support\Facades\Http;

test('zenserp search route is rate limited using package config', function () {
    config()->set('zenserp.rate_limit.max_attempts', 2);
    config()->set('zenserp.rate_limit.decay_seconds', 60);

    Http::fake([
        'https://app.zenserp.com/*' => Http::response(['ok' => true], 200),
    ]);

    $this->get(route('zenserp.search', ['q' => 'netwatchglobal']))->assertOk();
    $this->get(route('zenserp.search', ['q' => 'netwatchglobal']))->assertOk();
    $this->get(route('zenserp.search', ['q' => 'netwatchglobal']))->assertTooManyRequests();
});
