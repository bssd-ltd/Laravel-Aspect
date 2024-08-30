<?php

use Ytake\LaravelAspect\AspectManager;
use Ytake\LaravelAspect\NullAspectKernel;
use Ytake\LaravelAspect\RayAspectKernel;

class AspectManagerTest extends AspectTestCase
{
    /** @var AspectManager $manager */
    protected $manager;

    public function testCreateDriverInstance()
    {
        $this->assertIsString($this->manager->getDefaultDriver());
    }

    public function testCreateGoDriverInstance()
    {
        $this->assertInstanceOf(
            RayAspectKernel::class, $this->manager->driver('ray')
        );
    }

    public function testCreateNullDriverInstance()
    {
        /** @var NullAspectKernel $driver */
        $driver = $this->manager->driver('none');
        $this->assertInstanceOf(NullAspectKernel::class, $driver);
        $this->assertNull($driver->register());
        $class = new ReflectionClass($driver);
        $this->assertSame(0, count($class->getProperties()));
        $this->assertNull($driver->weave());
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->manager = new AspectManager($this->app);
    }
}
