<?php

use __Test\AspectLoggable;
use __Test\CacheableModule;
use __Test\CacheEvictModule;
use __Test\LoggableModule;
use Illuminate\Filesystem\Filesystem;
use Ytake\LaravelAspect\AspectManager;
use Ytake\LaravelAspect\RayAspectKernel;

/**
 * Class AspectLoggableTest
 */
class AspectLoggableTest extends AspectTestCase
{
    /** @var AspectManager $manager */
    protected $manager;

    /** @var Illuminate\Log\Writer */
    protected $log;

    /** @var Filesystem */
    protected $file;

    public function testDefaultLogger()
    {
        /** @var AspectLoggable $cache */
        $cache = $this->app->make(AspectLoggable::class);
        $cache->normalLog(1);
        $put = $this->app['files']->get($this->logDir() . '/.testing.log');
        $this->assertStringContainsString('Loggable:__Test\AspectLoggable.normalLog', $put);
        $this->assertStringContainsString('{"args":{"id":1},"result":1', $put);
    }

    public function testSkipResultLogger()
    {
        /** @var AspectLoggable $cache */
        $cache = $this->app->make(AspectLoggable::class);
        $cache->skipResultLog(1);
        $put = $this->app['files']->get($this->logDir() . '/.testing.log');
        $this->assertStringContainsString('Loggable:__Test\AspectLoggable.skipResultLog', $put);
        $this->assertStringNotContainsString('"result":1', $put);
    }

    public function tearDown(): void
    {
        $this->app['files']->deleteDirectory($this->logDir());
        parent::tearDown();
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->manager = new AspectManager($this->app);
        $this->resolveManager();
        $this->log = $this->app['Psr\Log\LoggerInterface'];
        $this->file = $this->app['files'];
        if (!$this->app['files']->exists($this->logDir())) {
            $this->app['files']->makeDirectory($this->logDir());
        }
    }

    /**
     *
     */
    protected function resolveManager()
    {
        /** @var RayAspectKernel $aspect */
        $aspect = $this->manager->driver('ray');
        $aspect->register(LoggableModule::class);
        $aspect->register(CacheEvictModule::class);
        $aspect->register(CacheableModule::class);
        $aspect->weave();
    }
}
