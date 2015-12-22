<?php

/**
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 *
 * Copyright (c) 2015 Yuuki Takezawa
 *
 */
namespace Ytake\LaravelAspect\Modules;

use Ray\Aop\Bind;
use Ray\Aop\CompilerInterface;
use Illuminate\Contracts\Container\Container as Application;

/**
 * Class AspectModule
 */
abstract class AspectModule
{
    /** @var Application */
    protected $app;

    /** @var Bind */
    protected $bind;

    /** @var CompilerInterface */
    protected $compiler;

    /** @var array  */
    protected static $pointcuts = [];

    /** @var array  */
    protected static $resolve = [];

    /**
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @return void
     */
    abstract public function attach();

    /**
     * @param string $class
     */
    protected function instanceResolver($class)
    {
        self::$resolve[$class] = self::$pointcuts;
    }

    /**
     * @return array
     */
    public function getResolver()
    {
        return self::$resolve;
    }
}