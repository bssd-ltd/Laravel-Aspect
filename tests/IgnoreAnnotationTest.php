<?php

use __Test\AnnotationStub;
use __Test\LogExceptionsModule;
use Ytake\LaravelAspect\AspectManager;
use Ytake\LaravelAspect\RayAspectKernel;

/**
 * Class IgnoreAnnotationTest
 */
class IgnoreAnnotationTest extends AspectTestCase
{
    /** @var AspectManager $manager */
    protected $manager;

    public function testGenerateCacheNameRemoveNullKey()
    {
        /** @var AnnotationStub $class */
        $class = $this->app->make(AnnotationStub::class);
        $this->assertNull($class->testing());
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
        $aspect->register(LogExceptionsModule::class);
        $aspect->weave();
    }
}
