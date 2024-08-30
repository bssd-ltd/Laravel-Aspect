<?php
declare(strict_types=1);

use __Test\AspectMessageDriven;
use __Test\MessageDrivenModule;
use Doctrine\Common\Annotations\AnnotationReader;
use Illuminate\Container\Container;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\Container\BindingResolutionException;
use Ray\Aop\MethodInvocation;
use Ytake\LaravelAspect\Annotation\MessageDriven;
use Ytake\LaravelAspect\AspectManager;
use Ytake\LaravelAspect\Interceptor\MessageDrivenInterceptor;

final class MessageDrivenInterceptorTest extends AspectTestCase
{
    /** @var AspectManager $manager */
    protected $manager;

    /** @var  MessageDrivenInterceptor */
    private $interceptor;

    public function testShouldDispatchMethod(): void
    {
        $this->expectOutputString('this');
        $this->interceptor->setBusDispatcher(
            $this->app->make(Dispatcher::class)
        );
        $this->interceptor->setAnnotation(MessageDriven::class);
        $this->interceptor->invoke(
            new StubMessageDrivenInvocation()
        );
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->manager = new AspectManager($this->app);
        $this->resolveManager();
        $this->interceptor = new MessageDrivenInterceptor;
    }

    /**
     *
     */
    protected function resolveManager()
    {
        $aspect = $this->manager->driver('ray');
        $aspect->register(MessageDrivenModule::class);
        $aspect->weave();
    }
}

class StubMessageDrivenInvocation implements MethodInvocation
{
    /** @var ReflectionMethod */
    protected $reflectionMethod;

    public function getNamedArguments(): ArrayObject
    {
        return new ArrayObject([]);
    }

    public function getArguments(): ArrayObject
    {
        return new ArrayObject(['argument' => 'this']);
    }

    public function proceed()
    {
        return $this->intercept()->exec('this');
    }

    protected function intercept()
    {
        return new AspectMessageDriven;
    }

    public function getThis()
    {
        return new AspectMessageDriven();
    }

    /**
     * @return \Ray\Aop\ReflectionMethod
     * @throws ReflectionException
     * @throws BindingResolutionException
     */
    public function getMethod(): \Ray\Aop\ReflectionMethod
    {
        $reflectionClass = new ReflectionClass(AspectMessageDriven::class);
        $reflectionMethod = new \Ray\Aop\ReflectionMethod(AspectMessageDriven::class, 'exec');
        $reflectionMethod->setObject(
            Container::getInstance()->make(AspectMessageDriven::class, []),
            $reflectionClass->getMethod('exec')
        );

        return $reflectionMethod;
    }

    public function getName()
    {
        return $this->reflectionMethod->getName();
    }

    public function getAnnotation($name)
    {
        $reader = new AnnotationReader();

        return $reader->getMethodAnnotation($this->reflectionMethod, $name);
    }
}
