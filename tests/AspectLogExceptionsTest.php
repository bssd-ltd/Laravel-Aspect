<?php

use __Test\AspectLogExceptions;
use __Test\AspectLoggable;
use __Test\CacheableModule;
use __Test\CacheEvictModule;
use __Test\LogExceptionsModule;
use Illuminate\Filesystem\Filesystem;
use Ytake\LaravelAspect\AspectManager;
use Ytake\LaravelAspect\Exception\FileNotFoundException;
use Ytake\LaravelAspect\RayAspectKernel;

/**
 * Class AspectLogExceptionsTest
 */
class AspectLogExceptionsTest extends AspectTestCase
{
    /** @var AspectManager $manager */
    protected $manager;

    /** @var Illuminate\Log\Writer */
    protected $log;

    /** @var Filesystem */
    protected $file;

    /**
     */
    public function testDefaultLogger()
    {
        $this->expectException(Exception::class);
        // $this->log->useFiles($this->getDir() . '/.testing.exceptions.log');
        /** @var AspectLoggable $cache */
        $cache = $this->app->make(AspectLogExceptions::class);
        $cache->normalLog(1);
        $this->app['files']->deleteDirectory($this->getDir());
    }

    /**
     * @return string
     */
    protected function getDir()
    {
        return __DIR__ . '/storage/log';
    }

    public function testShouldBeLogger()
    {
        //$this->log->useFiles($this->getDir() . '/.testing.exceptions.log');
        /** @var AspectLoggable $cache */
        $cache = $this->app->make(AspectLogExceptions::class);
        try {
            $cache->normalLog(1);
        } catch (Exception $e) {
            $put = $this->app['files']->get($this->getDir() . '/.testing.exceptions.log');
            $this->assertStringContainsString('LogExceptions:__Test\AspectLogExceptions.normalLog', $put);
            $this->assertStringContainsString('"code":0,"error_message":"', $put);
        }
        $this->app['files']->deleteDirectory($this->getDir());
    }

    public function testNoException()
    {
        /** @var AspectLogExceptions $cache */
        $cache = $this->app->make(AspectLogExceptions::class);
        $this->assertSame(1, $cache->noException());
    }

    public function testExpectException()
    {
        // $this->log->useFiles($this->getDir() . '/.testing.exceptions.log');
        /** @var AspectLogExceptions $cache */
        $cache = $this->app->make(AspectLogExceptions::class);
        try {
            $cache->expectException();
        } catch (LogicException $e) {
            $put = $this->app['files']->get($this->getDir() . '/.testing.exceptions.log');
            $this->assertStringContainsString('LogExceptions:__Test\AspectLogExceptions.expectException', $put);
            $this->assertStringContainsString('"code":0,"error_message":"', $put);
        }
        $this->app['files']->deleteDirectory($this->getDir());
    }

    public function testShouldNotPutExceptionLoggerFile()
    {
        // $this->log->useFiles($this->getDir() . '/.testing.exceptions.log');
        /** @var AspectLogExceptions $logger */
        $logger = $this->app->make(AspectLogExceptions::class);
        try {
            $logger->expectNoException();
        } catch (FileNotFoundException $e) {
            $this->assertFileDoesNotExist($this->getDir() . '/.testing.exceptions.log');
        }
        $this->app['files']->deleteDirectory($this->getDir());
    }

    public function testShouldNotThrowableException()
    {
        /** @var AspectLogExceptions $logger */
        $logger = $this->app->make(AspectLogExceptions::class);
        $this->assertSame(1, $logger->noException());
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->manager = new AspectManager($this->app);
        $this->resolveManager();
        $this->log = $this->app['Psr\Log\LoggerInterface'];
        $this->file = $this->app['files'];
        if (!$this->app['files']->exists($this->getDir())) {
            $this->app['files']->makeDirectory($this->getDir());
        }
    }

    /**
     *
     */
    protected function resolveManager()
    {
        /** @var RayAspectKernel $aspect */
        $aspect = $this->manager->driver('ray');
        $aspect->register(LogExceptionsModule::class);
        $aspect->register(CacheEvictModule::class);
        $aspect->register(CacheableModule::class);
        $aspect->weave();
    }
}
