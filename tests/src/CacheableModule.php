<?php

namespace __Test;

use ResolveMockClass;

class CacheableModule extends \Ytake\LaravelAspect\Modules\CacheableModule
{
    /**
     * @var array
     */
    protected $classes = [
        AspectCacheable::class,
        AspectCacheEvict::class,
        AspectMerge::class,
        ResolveMockClass::class,
    ];
}
