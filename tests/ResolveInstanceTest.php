<?php

use __Test\AspectContextualBinding;
use __Test\CacheableModule;
use __Test\LoggableModule;
use Ytake\LaravelAspect\AspectManager;

/**
 * Class ResolveInstanceTest
 */
class ResolveInstanceTest extends AspectTestCase
{
    /** @var AspectManager $manager */
    protected $manager;

    public function testShouldReturnBindingConcreteClass()
    {
        $this->app->bind('a', ResolveMockClass::class);

        $this->app->bind(ResolveMockInterface::class, ResolveMockClass::class);

        $resolve = $this->app->make(ResolveMockInterface::class);
        $resolve->get();
        $this->assertSame(
            $resolve->get(),
            $this->app['cache']->get('testing.resolve.instance')
        );
        $this->assertInstanceOf(get_class($resolve), $this->app->make('a'));
    }

    public function testShouldReturnSameInstanceForShared()
    {
        $this->app->singleton(ResolveMockInterface::class, ResolveMockClass::class);
        $resolve = $this->app->make(ResolveMockInterface::class);
        $this->assertSame($resolve, $this->app->make(ResolveMockInterface::class));
    }

    public function testShouldResolveContextualBinding()
    {
        $log = $this->app['Psr\Log\LoggerInterface'];
        if (!$this->app['files']->exists($this->getDir())) {
            $this->app['files']->makeDirectory($this->getDir());
        }
        $this->app->when(AspectContextualBinding::class)
            ->needs(ResolveMockInterface::class)
            ->give(ResolveMockClass::class);
        /** @var  AspectManager $aspectManager */
        $aspectManager = $this->app['aspect.manager'];
        $driver = $aspectManager->driver('ray');
        $driver->register(CacheableModule::class);
        $driver->register(LoggableModule::class);
        $driver->weave();
        /** @var AspectContextualBinding $concrete */
        $concrete = $this->app->make(AspectContextualBinding::class);
        $result = $concrete->testing();
        sleep(1);
        $this->assertSame($result, $concrete->testing());
        $this->app['files']->deleteDirectory($this->getDir());
    }

    /**
     * @return string
     */
    protected function getDir()
    {
        return __DIR__ . '/storage/log';
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
        $aspect->register(CacheableModule::class);
        $aspect->register(LoggableModule::class);
        $aspect->weave();
    }
}
