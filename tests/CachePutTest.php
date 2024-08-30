<?php

use __Test\AspectCachePut;
use __Test\CachePutModule;
use Ytake\LaravelAspect\AspectManager;

class CachePutTest extends AspectTestCase
{
    protected static $instance;
    /** @var AspectManager $manager */
    protected $manager;

    public function testCachePutDefaultValues()
    {
        $cache = $this->app->make(AspectCachePut::class);
        $this->app['cache']->add('singleKey:1000', 1, 120);
        $this->assertNull($cache->singleKey());
    }

    public function testCachePutReturnUpdatedValue()
    {
        $cache = $this->app->make(AspectCachePut::class);
        $this->app['cache']->add('singleKey:1000', 1, 120);
        $result = $cache->singleKey(1000);
        $this->assertSame(1000, $result);
        $this->assertSame(1000, $this->app['cache']->get('singleKey:1000'));
    }

    public function testCacheableGenerateCacheNameSingleKey()
    {
        $this->expectException(InvalidArgumentException::class);
        $cache = $this->app->make(AspectCachePut::class);
        $cache->throwExceptionCache();
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
        $aspect->register(CachePutModule::class);
        $aspect->weave();
    }
}
