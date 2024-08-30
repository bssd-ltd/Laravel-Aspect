<?php

use Illuminate\Filesystem\Filesystem;
use Ray\Aop\Bind;
use Ytake\LaravelAspect\AspectBind;

/**
 * Class AspectBindTest
 */
class AspectBindTest extends AspectTestCase
{
    /** @var Filesystem */
    protected $file;

    public function setUp(): void
    {
        parent::setUp();
        $this->file = $this->app['files'];
    }

    public function testShouldReturnNoCacheableBindInstance()
    {
        $bind = new AspectBind(
            $this->file,
            $this->getDir(),
            false
        );
        $this->assertInstanceOf(Bind::class, $bind->bind(StubBindableClass::class, []));
        $this->assertFalse($this->file->exists($this->getDir()));
    }

    /**
     * @return string
     */
    protected function getDir()
    {
        return __DIR__ . '/storage/tmp';
    }

    public function testShouldReturnCacheableBindInstance()
    {
        $bind = new AspectBind(
            $this->file,
            $this->getDir(),
            true
        );
        $this->assertInstanceOf(Bind::class, $bind->bind(StubBindableClass::class, []));
        $this->assertTrue($this->file->exists($this->getDir()));
    }

    public function tearDown(): void
    {
        $this->file->deleteDirectory($this->getDir());
        parent::tearDown();
    }
}

/**
 * Class StubBindableClass
 */
class StubBindableClass
{

}