<?php

namespace __Test;

use Ytake\LaravelAspect\Modules\LogExceptionsModule as Loggable;

class LogExceptionsModule extends Loggable
{
    /**
     * @var array
     */
    protected $classes = [
        AspectLogExceptions::class,
        AnnotationStub::class
    ];
}
