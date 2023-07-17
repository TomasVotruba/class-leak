<?php

declare(strict_types=1);

use TomasVotruba\ClassLeak\Providers\AppServiceProvider;

use TomasVotruba\PunchCard\AppConfig;

return AppConfig::make()
    ->providers([AppServiceProvider::class])
    ->toArray();
