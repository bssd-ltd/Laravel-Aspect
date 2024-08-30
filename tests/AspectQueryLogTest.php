<?php

use __Test\AspectQueryLog;
use __Test\LoggableModule;
use __Test\QueryLogModule;
use __Test\TransactionalModule;
use Illuminate\Filesystem\Filesystem;
use Ytake\LaravelAspect\AspectManager;
use Ytake\LaravelAspect\RayAspectKernel;

/**
 * Class AspectQueryLogTest
 */
class AspectQueryLogTest extends AspectTestCase
{
    /** @var AspectManager $manager */
    protected $manager;

    /** @var Illuminate\Log\Writer */
    protected $log;

    /** @var Filesystem */
    protected $file;

    public function testDefaultLogger()
    {
        /** @var AspectQueryLog $concrete */
        $concrete = $this->app->make(AspectQueryLog::class);
        $concrete->start();
        $put = $this->app['files']->get($this->logDir() . '/.testing.log');
        $this->assertStringContainsString('INFO: QueryLog:__Test\AspectQueryLog.start', $put);
        $this->assertStringContainsString('SELECT date(\'now\')', $put);
    }

    public function testTransactionalLogger()
    {
        /** @var AspectQueryLog $concrete */
        $concrete = $this->app->make(AspectQueryLog::class);
        $concrete->multipleDatabaseAppendRecord();
        $put = $this->app['files']->get($this->logDir() . '/.testing.log');
        $this->assertStringContainsString('INFO: QueryLog:__Test\AspectQueryLog.multipleDatabaseAppendRecord', $put);
        $this->assertStringContainsString('"queries":[{"query":"CREATE TABLE tests (test varchar(255) NOT NULL)"', $put);
    }

    public function testExceptionalDatabaseLogger()
    {
        $this->expectException(Exception::class);
        /** @var AspectQueryLog $concrete */
        $concrete = $this->app->make(AspectQueryLog::class);
        $concrete->appendRecord(['test' => 'testing']);
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
        $aspect->register(QueryLogModule::class);
        $aspect->register(TransactionalModule::class);
        $aspect->weave();
    }
}
