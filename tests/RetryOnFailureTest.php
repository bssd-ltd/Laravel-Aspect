<?php

use __Test\AspectRetryOnFailure;
use __Test\RetryOnFailureModule;
use Ytake\LaravelAspect\AspectManager;

/**
 * Class RetryOnFailureTest
 */
class RetryOnFailureTest extends AspectTestCase
{
    /** @var AspectManager $manager */
    protected $manager;

    public function testShouldThrowWithRetry()
    {
        $concrete = $this->app->make(AspectRetryOnFailure::class);
        try {
            $concrete->call();
        } catch (Exception $e) {
            $this->assertSame(3, $concrete->counter);
            $concrete->counter = 0;
        }
        try {
            $concrete->call();
        } catch (Exception $e) {
            $this->assertSame(3, $concrete->counter);
            $concrete->counter = 0;
        }

        try {
            $concrete->ignoreException();
        } catch (Exception $e) {
            $this->assertSame(1, $concrete->counter);
        }
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->manager = new AspectManager($this->app);
        $this->resolveManager();
    }

    protected function resolveManager()
    {
        $aspect = $this->manager->driver('ray');
        $aspect->register(RetryOnFailureModule::class);
        $aspect->weave();
    }
}
