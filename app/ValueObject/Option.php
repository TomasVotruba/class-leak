<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\ValueObject;

final class Option
{
    /**
     * @var string
     */
    public const SOURCES = 'sources';

    /**
     * @deprecated Use EasyCIConfig instead
     * @var string
     */
    public const TYPES_TO_SKIP = 'types_to_skip';
}
