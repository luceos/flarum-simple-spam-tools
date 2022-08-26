<?php

namespace Luceos\Spam\Tests;

use Flarum\Foundation\Config;
use Illuminate\Container\Container;

class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $container = Container::getInstance();

        $container->singleton(Config::class, fn() => new Config([
            'url' => 'http://localhost'
        ]));
    }
}
