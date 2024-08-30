<?php

use __Test\AspectPostConstruct;
use __Test\PostConstructModule;
use Ytake\LaravelAspect\AspectManager;
use Ytake\LaravelAspect\RayAspectKernel;

/**
 * Class PostConstructTest
 */
class PostConstructTest extends AspectTestCase
{
    /** @var AspectManager $manager */
    protected $manager;

    public function testShouldProceedPostConstructSumVariable()
    {
        /** @var AspectPostConstruct $class */
        $class = $this->app->make(AspectPostConstruct::class, ['a' => 1]);
        $this->assertInstanceOf(AspectPostConstruct::class, $class);
        $this->assertSame(2, $class->getA());
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->manager = new AspectManager($this->app);
        $this->resolveManager();
    }

    protected function resolveManager()
    {
        /** @var RayAspectKernel $aspect */
        $aspect = $this->manager->driver('ray');
        $aspect->register(PostConstructModule::class);
        $aspect->weave();
    }
}
