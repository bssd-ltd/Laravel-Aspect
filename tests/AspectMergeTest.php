<?php

use __Test\AspectMerge;
use __Test\CacheableModule;
use __Test\CacheEvictModule;
use Ytake\LaravelAspect\AspectManager;
use Ytake\LaravelAspect\RayAspectKernel;

/**
 * AspectMergeTest.php
 */
class AspectMergeTest extends AspectTestCase
{
    protected static $instance;
    /** @var AspectManager $manager */
    protected $manager;

    public function testCacheAspects()
    {
        /** @var AspectMerge $cache */
        $cache = $this->app->make(AspectMerge::class);
        $cache->caching(1);
        $result = $this->app['cache']->tags(['testing1', 'testing2'])->get('caching:1');
        $this->assertNull($result);
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
        /** @var RayAspectKernel $aspect */
        $aspect = $this->manager->driver('ray');
        $aspect->register(CacheableModule::class);
        $aspect->register(CacheEvictModule::class);
        $aspect->weave();
    }
}
