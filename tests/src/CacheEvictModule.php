<?php

namespace __Test;

class CacheEvictModule extends \Ytake\LaravelAspect\Modules\CacheEvictModule
{
    /**
     * @var array
     */
    protected $classes = [
        AspectCacheEvict::class,
        AspectMerge::class
    ];
}
