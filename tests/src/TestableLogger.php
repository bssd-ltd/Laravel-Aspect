<?php
declare(strict_types=1);

namespace __Test;

use Exception;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

class TestableLogger
{
    /**
     * @param array $config
     *
     * @return LoggerInterface
     * @throws Exception
     */
    public function __invoke(array $config): LoggerInterface
    {
        return new Logger('testing', [
            new StreamHandler($config['path'])
        ]);
    }
}
