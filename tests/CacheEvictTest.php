<?php

use __Test\AspectCacheEvict;
use __Test\CacheableModule;
use __Test\CacheEvictModule;
use Ytake\LaravelAspect\AspectManager;

class CacheEvictTest extends AspectTestCase
{
    /** @var AspectManager $manager */
    protected $manager;

    public function testGenerateCacheNameRemoveNullKey()
    {
        /** @var AspectCacheEvict $cache */
        $cache = $this->app->make(AspectCacheEvict::class);
        $cache->singleCacheDelete();
        $this->assertNull($this->app['cache']->get('singleCacheDelete'));
    }

    public function testCacheableAndRemove()
    {
        /** @var AspectCacheEvict $cache */
        $cache = $this->app->make(AspectCacheEvict::class);
        $cache->cached(1, 2);
        $this->assertNotNull($this->app['cache']->tags(['testing1'])->get('testing:1:2'));

        // flush all entries
        $cache->removeCache();
        $this->assertNull($this->app['cache']->tags(['testing1'])->get('testing:1:2'));
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->manager = new AspectManager($this->app);
        $this->resolveManager();
    }

    /**
     *
     */
    protected function resolveManager()
    {
        $aspect = $this->app['aspect.manager']->driver('ray');
        $aspect->register(CacheEvictModule::class);
        $aspect->register(CacheableModule::class);
        $aspect->weave();
    }
}
