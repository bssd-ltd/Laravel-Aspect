<?php

use __Test\AspectCacheable;
use __Test\CacheableModule;
use Illuminate\Cache\CacheManager;
use Ytake\LaravelAspect\AspectManager;

class CacheableTest extends AspectTestCase
{
    /** @var AspectManager $manager */
    protected $manager;

    public function testCacheableGenerateCacheNameSingleKey()
    {
        /** @var CacheManager $manager */
        $cache = $this->app->make(AspectCacheable::class);
        $result = $cache->singleKey(1000);
        $this->assertSame(1000, $result);
        // null cache driver always return null
        $this->assertNull($this->app['cache']->get('singleKey:1000'));

        $result = $cache->multipleKey(1000, 'testing');
        $this->assertSame(1000, $result);
        $this->assertSame(1000, $this->app['cache']->get('multipleKey:1000:testing'));
        $manager = $this->app['cache'];
        $this->assertSame('array', $manager->getDefaultDriver());

        $result = $cache->multipleKey(1000, 'testing');
        $this->assertSame(1000, $result);
        $this->assertNull($this->app['cache']->get('singleKey:1000'));
    }

    public function testCacheableGenerateCacheNameMultipleKey()
    {
        $cache = $this->app->make(AspectCacheable::class);
        $result = $cache->multipleKey(1000, 'testing');
        $this->assertSame(1000, $result);
        $this->assertSame(1000, $this->app['cache']->get('multipleKey:1000:testing'));
    }

    public function testCacheableCacheNameMultipleKey()
    {
        $cache = $this->app->make(AspectCacheable::class);
        $result = $cache->namedMultipleKey(1000, 'testing');
        $this->assertSame(1000, $result);
        $this->assertSame(1000, $this->app['cache']->get('testing1:1000:testing'));
    }

    public function testCacheableCacheNameMultipleNameAndKey()
    {
        $cache = $this->app->make(AspectCacheable::class);
        $result = $cache->namedMultipleNameAndKey(1000, 'testing');
        $this->assertSame(1000, $result);
        $this->assertSame(1000, $this->app['cache']->tags(['testing1', 'testing2'])->get('namedMultipleNameAndKey:1000:testing'));
    }

    public function testCacheableCacheObject()
    {
        $cache = $this->app->make(AspectCacheable::class);
        $class = new stdClass;
        $class->title = 'testing';
        $result = $cache->cachingKeyObject(1000, $class);
        $this->assertSame(1000, $result);
    }

    public function testShouldBeNullForNegativeCache()
    {
        /** @var AspectCacheable $cache */
        $cache = $this->app->make(AspectCacheable::class);
        $this->assertNull($cache->negativeCache());
        $this->assertNull($this->app['cache']->get('negative'));
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
        $aspect = $this->manager->driver('ray');
        $aspect->register(CacheableModule::class);
        $aspect->weave();
    }
}
