<?php

declare(strict_types=1);

use TomasVotruba\PunchCard\AppConfig;

return AppConfig::make()
    ->providers([\TomasVotruba\ClassLeak\Providers\AppServiceProvider::class])
    ->toArray();
