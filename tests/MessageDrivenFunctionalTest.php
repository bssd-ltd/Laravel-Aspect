<?php

use __Test\AspectMessageDriven;
use __Test\LoggableModule;
use __Test\MessageDrivenModule;
use Illuminate\Filesystem\Filesystem;
use Ytake\LaravelAspect\AspectManager;
use Ytake\LaravelAspect\RayAspectKernel;

/**
 * Class MessageDrivenFunctionalTest
 */
class MessageDrivenFunctionalTest extends AspectTestCase
{
    /** @var AspectManager $manager */
    protected $manager;

    /** @var Illuminate\Log\Writer */
    protected $log;

    /** @var Filesystem */
    protected $file;

    public function testShouldBeLazyQueue()
    {
        $this->expectOutputString('this');
        /** @var AspectMessageDriven $concrete */
        $concrete = $this->app->make(AspectMessageDriven::class);
        $concrete->exec('this');
        $put = $this->app['files']->get($this->logDir() . '/.testing.log');
        $this->assertStringContainsString('Loggable:__Test\AspectMessageDriven.exec {"args":{"param":"this"}', $put);
        $this->assertStringContainsString('Queued:__Test\AspectMessageDriven.logWith', $put);
    }

    public function testShouldBeEagerQueue()
    {
        /** @var AspectMessageDriven $concrete */
        $concrete = $this->app->make(AspectMessageDriven::class);
        $concrete->eagerExec('testing');
        $put = $this->app['files']->get($this->logDir() . '/.testing.log');
        $this->assertStringContainsString('Queued:__Test\AspectMessageDriven.logWith', $put);
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

    protected function resolveManager()
    {
        /** @var RayAspectKernel $aspect */
        $aspect = $this->manager->driver('ray');
        $aspect->register(MessageDrivenModule::class);
        $aspect->register(LoggableModule::class);
        $aspect->weave();
    }
}
