<?php

use App\Providers\AppServiceProvider;
use App\Providers\FortifyServiceProvider;
use Zenserp\Providers\ZenserpServiceProvider;

return [
    AppServiceProvider::class,
    FortifyServiceProvider::class,
    ZenserpServiceProvider::class,
];
