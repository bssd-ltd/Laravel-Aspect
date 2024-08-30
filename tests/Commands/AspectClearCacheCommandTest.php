<?php

use __Test\AspectCacheable;
use __Test\CacheableModule;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Ytake\LaravelAspect\AspectManager;
use Ytake\LaravelAspect\Console\ClearCacheCommand;

/**
 * Class AspectClearCacheCommandTest
 */
class AspectClearCacheCommandTest extends AspectTestCase
{
    /** @var AspectManager $manager */
    protected $manager;

    /** @var ClearCacheCommand */
    protected $command;

    public function testCacheClearFile()
    {
        $cache = $this->app->make(AspectCacheable::class);
        $cache->namedMultipleNameAndKey(1000, 'testing');

        $output = new BufferedOutput();
        $this->command->run(
            new ArrayInput([]),
            $output
        );
        $this->assertSame('aspect code cache clear!', trim($output->fetch()));

        $configure = $this->app['config']->get('ytake-laravel-aop');
        $driverConfig = $configure['aspect']['drivers'][$configure['aspect']['default']];
        if (isset($driverConfig['cache_dir'])) {
            $files = $this->app['files']->files($driverConfig['cache_dir']);
            $this->assertCount(0, $files);
        }
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->manager = new AspectManager($this->app);
        $this->resolveManager();

        $this->command = new ClearCacheCommand(
            $this->app['config'],
            $this->app['files']
        );
        $this->command->setLaravel(new MockApplication());
    }

    /**
     *
     */
    protected function resolveManager()
    {
        $aspect = $this->manager->driver('ray');
        $aspect->register(CacheableModule::class);
    }
}
