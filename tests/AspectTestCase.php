<?php

use Illuminate\Bus\BusServiceProvider;
use Illuminate\Cache\CacheManager;
use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Contracts\Cache\Factory;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Connectors\ConnectionFactory;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Encryption\EncryptionServiceProvider;
use Illuminate\Events\EventServiceProvider;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Log\LogManager;
use Illuminate\Queue\QueueServiceProvider;
use PHPUnit\Framework\TestCase;
use Ytake\LaravelAspect\AnnotationConfiguration;
use Ytake\LaravelAspect\AspectManager;

/**
 * Class AspectTestCase
 */
class AspectTestCase extends TestCase
{
    /** @var Container $app */
    protected $app;

    protected function setUp(): void
    {
        $this->createApplicationContainer();
    }

    protected function createApplicationContainer()
    {
        $this->app = new class() extends Container {
            public function storagePath()
            {
                return __DIR__;
            }

            public function runningUnitTests()
            {
                return true;
            }
        };
        $this->app->singleton('config', function () {
            return new Repository;
        });
        $logManager = new LogManager($this->app);
        $this->app->instance('log', $logManager);
        $this->app->instance('Psr\Log\LoggerInterface', $logManager);
        $eventProvider = new EventServiceProvider($this->app);
        $eventProvider->register();
        $busServiceProvider = new BusServiceProvider($this->app);
        $busServiceProvider->register();
        $queueServiceProvider = new QueueServiceProvider($this->app);
        $queueServiceProvider->register();
        $encryptionServiceProvider = new EncryptionServiceProvider($this->app);
        $encryptionServiceProvider->register();
        $this->app->alias('queue', \Illuminate\Contracts\Queue\Factory::class);
        $this->app->alias('events', Dispatcher::class);
        $this->registerConfigure();
        $this->registerDatabase();
        $this->registerCache();
        $annotationConfiguration = new AnnotationConfiguration(
            $this->app['config']->get('ytake-laravel-aop.annotation')
        );
        $annotationConfiguration->ignoredAnnotations();
        $this->app->singleton('aspect.manager', function ($app) {
            return new AspectManager($app);
        });
        $this->app->bind(
            \Illuminate\Contracts\Container\Container::class,
            Container::class
        );
        $this->app->bind(
            Container::class,
            function () {
                return $this->app;
            }
        );
        Container::setInstance($this->app);
    }

    /**
     * @throws FileNotFoundException
     */
    protected function registerConfigure()
    {
        $filesystem = new Filesystem;

        $this->app['config']->set(
            "ytake-laravel-aop",
            $filesystem->getRequire(__DIR__ . '/config/ytake-laravel-aop.php')
        );
        $this->app['config']->set(
            "database",
            $filesystem->getRequire(__DIR__ . '/config/database.php')
        );
        $this->app['config']->set(
            "cache",
            $filesystem->getRequire(__DIR__ . '/config/cache.php')
        );
        $this->app['config']->set(
            'queue',
            $filesystem->getRequire(__DIR__ . '/config/queue.php')
        );
        $this->app['config']->set(
            'logging',
            $filesystem->getRequire(__DIR__ . '/config/logging.php')
        );
        $this->app['config']->set(
            'app',
            [
                'key' => 'base64:vL6wZyxF+/4DhgKiNoA3k80pwdX2VwvLDSig9juMk8g=',
                'cipher' => 'AES-256-CBC',
            ]
        );
        $this->app['files'] = $filesystem;
    }

    protected function registerDatabase()
    {
        Model::clearBootedModels();
        $this->app->singleton('db.factory', function ($app) {
            return new ConnectionFactory($app);
        });
        $this->app->singleton('db', function ($app) {
            return new DatabaseManager($app, $app['db.factory']);
        });
        $this->app->alias('db', DatabaseManager::class);
        $this->app->bind('Illuminate\Database\ConnectionResolverInterface', DatabaseManager::class);
    }

    protected function registerCache()
    {
        $this->app->singleton('cache', function ($app) {
            return new CacheManager($app);
        });

        $this->app->singleton('cache.store', function ($app) {
            return $app['cache']->driver();
        });
        $this->app->alias('cache', Factory::class);
    }

    /**
     * @return string
     */
    protected function logDir()
    {
        return __DIR__ . '/storage/log';
    }
}
