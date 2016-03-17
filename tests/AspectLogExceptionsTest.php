<?php

/**
 * Class AspectLogExceptionsTest
 */
class AspectLogExceptionsTest extends \TestCase
{
    /** @var \Ytake\LaravelAspect\AspectManager $manager */
    protected $manager;

    /** @var Illuminate\Log\Writer */
    protected $log;

    /** @var \Illuminate\Filesystem\Filesystem */
    protected $file;

    protected function setUp()
    {
        parent::setUp();
        $this->manager = new \Ytake\LaravelAspect\AspectManager($this->app);
        $this->resolveManager();
        $this->log = $this->app['log'];
        $this->file = $this->app['files'];
        if (!$this->file->exists($this->getDir())) {
            $this->file->makeDirectory($this->getDir());
        }
    }

    /**
     * @expectedException \Exception
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function testDefaultLogger()
    {
        $this->log->useFiles($this->getDir() . '/.testing.exceptions.log');
        /** @var \__Test\AspectLoggable $cache */
        $cache = $this->app->make(\__Test\AspectLogExceptions::class);
        $cache->normalLog(1);
    }

    public function testShouldBeLogger()
    {
        $this->log->useFiles($this->getDir() . '/.testing.exceptions.log');
        /** @var \__Test\AspectLoggable $cache */
        $cache = $this->app->make(\__Test\AspectLogExceptions::class);
        try {
            $cache->normalLog(1);
        } catch (\Exception $e) {
            $put = $this->file->get($this->getDir() . '/.testing.exceptions.log');
            $this->assertContains('LogExceptions:__Test\AspectLogExceptions.normalLog', $put);
            $this->assertContains('"code":0,"error_message":"', $put);
        }
    }

    public function testNoException()
    {
        /** @var \__Test\AspectLogExceptions $cache */
        $cache = $this->app->make(\__Test\AspectLogExceptions::class);
        $this->assertSame(1, $cache->noException());
    }

    public function testExpectException()
    {
        $this->log->useFiles($this->getDir() . '/.testing.exceptions.log');
        /** @var \__Test\AspectLogExceptions $cache */
        $cache = $this->app->make(\__Test\AspectLogExceptions::class);
        try {
            $cache->expectException();
        } catch (\LogicException $e) {
            $put = $this->file->get($this->getDir() . '/.testing.exceptions.log');
            $this->assertContains('LogExceptions:__Test\AspectLogExceptions.expectException', $put);
            $this->assertContains('"code":0,"error_message":"', $put);
        }
    }

    public function testShouldNotPutExceptionLoggerFile()
    {
        $this->log->useFiles($this->getDir() . '/.testing.exceptions.log');
        /** @var \__Test\AspectLogExceptions $logger */
        $logger = $this->app->make(\__Test\AspectLogExceptions::class);
        try {
            $logger->expectNoException();
        } catch (\Ytake\LaravelAspect\Exception\FileNotFoundException $e) {
            $this->assertFileNotExists($this->getDir() . '/.testing.exceptions.log');
        }
    }

    public function testShouldNotThrowableException()
    {
        /** @var \__Test\AspectLogExceptions $logger */
        $logger = $this->app->make(\__Test\AspectLogExceptions::class);
        $this->assertSame(1, $logger->noException());
    }

    public function tearDown()
    {
        $this->file->deleteDirectory($this->getDir());
        parent::tearDown();
    }

    /**
     *
     */
    protected function resolveManager()
    {
        /** @var \Ytake\LaravelAspect\RayAspectKernel $aspect */
        $aspect = $this->manager->driver('ray');
        $aspect->register(\__Test\LogExceptionsModule::class);
        $aspect->dispatch();
    }

    /**
     * @return string
     */
    protected function getDir()
    {
        return __DIR__ . '/storage/log';
    }
}