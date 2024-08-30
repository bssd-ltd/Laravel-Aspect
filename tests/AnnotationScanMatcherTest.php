<?php

use __Test\AspectPostConstruct;
use __Test\FailedPostConstruct;
use Ytake\LaravelAspect\Annotation\PostConstruct;
use Ytake\LaravelAspect\Matcher\AnnotationScanMatcher;

/**
 * Class AnnotationScanMatcherTest
 */
class AnnotationScanMatcherTest extends AspectTestCase
{
    /** @var AnnotationScanMatcher */
    private $matcher;

    public function testShouldBeBoolean()
    {
        $result = $this->matcher->matchesClass(new ReflectionClass(AspectPostConstruct::class), [
            PostConstruct::class
        ]);
        $this->assertTrue($result);
        $result = $this->matcher->matchesClass(new ReflectionClass(FailedPostConstruct::class), [
            PostConstruct::class
        ]);
        $this->assertFalse($result);

        $reflectionClass = new ReflectionClass(AspectPostConstruct::class);
        $result = $this->matcher->matchesMethod($reflectionClass->getMethod('getA'), [
            PostConstruct::class
        ]);
        $this->assertTrue($result);
        $reflectionClass = new ReflectionClass(FailedPostConstruct::class);
        $result = $this->matcher->matchesMethod($reflectionClass->getMethod('getA'), [
            PostConstruct::class
        ]);
        $this->assertFalse($result);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->matcher = new AnnotationScanMatcher;
    }
}
