<?php

use __Test\AspectTransactionalDatabase;
use __Test\AspectTransactionalString;
use __Test\TransactionalModule;
use Illuminate\Database\QueryException;
use Ytake\LaravelAspect\AspectDriverInterface;
use Ytake\LaravelAspect\AspectManager;

class TransactionalTest extends AspectTestCase
{

    protected static $instance;
    /** @var AspectManager $manager */
    protected $manager;

    public function testTransactionalAssertString()
    {
        $transactional = $this->app->make(AspectTransactionalString::class);
        $this->assertStringContainsString('testing', $transactional->start());
    }

    public function testTransactionalDatabase()
    {
        $transactional = $this->app->make(AspectTransactionalDatabase::class);
        $this->assertIsArray($transactional->start());
        $this->assertInstanceOf('stdClass', $transactional->start()[0]);
    }

    public function testTransactionalDatabaseThrowException()
    {
        $this->expectException(QueryException::class);
        /** @var AspectTransactionalDatabase $transactional */
        $transactional = $this->app->make(AspectTransactionalDatabase::class);
        try {
            $transactional->error();
        } catch (QueryException $e) {
            $this->assertNull($this->app['db']->connection()->table("tests")->where('test', 'testing')->first());
        }
    }

    public function testTransactionalDatabaseThrowLogicException()
    {
        $this->expectException(QueryException::class);
        /** @var AspectTransactionalDatabase $transactional */
        $transactional = $this->app->make(AspectTransactionalDatabase::class);
        try {
            $transactional->errorException();
        } catch (LogicException $e) {
            $this->assertNull($this->app['db']->connection()->table("tests")->where('test', 'testing')->first());
        }
    }

    public function testShouldReturnAppendRecord()
    {
        /** @var AspectTransactionalDatabase $transactional */
        $transactional = $this->app->make(AspectTransactionalDatabase::class);
        // return method
        $this->assertTrue($transactional->appendRecord(['test' => 'testing']));
        $result = $this->app['db']->connection()->table("tests")->where('test', 'testing')->first();
        $this->assertObjectHasAttribute('test', $result);
    }

    public function testTransactionalMultipleDatabaseThrowException()
    {
        $this->expectException(QueryException::class);
        /** @var AspectTransactionalDatabase $transactional */
        $transactional = $this->app->make(AspectTransactionalDatabase::class);
        try {
            $transactional->multipleDatabaseAppendRecordException();
        } catch (QueryException $e) {
            $this->assertNull($this->app['db']->connection()->table("tests")->where('test', 'testing')->first());
            $this->assertNull($this->app['db']->connection('testing_second')->table("tests")->where('test',
                'testing')->first());
        }
    }

    public function testShouldReturnStringTransactionalMultipleDatabase()
    {
        /** @var AspectTransactionalDatabase $transactional */
        $transactional = $this->app->make(AspectTransactionalDatabase::class);
        $this->assertSame('transaction test', $transactional->multipleDatabaseAppendRecord());
        $result = $this->app['db']->connection()->table("tests")->where('test', 'testing')->first();
        $this->assertObjectHasAttribute('test', $result);
        $result = $this->app['db']->connection('testing_second')->table("tests")->where('test', 'testing second')->first();
        $this->assertObjectHasAttribute('test', $result);
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
        /** @var AspectDriverInterface $aspect */
        $aspect = $this->manager->driver('ray');
        $aspect->register(TransactionalModule::class);
        $aspect->weave();
    }
}
