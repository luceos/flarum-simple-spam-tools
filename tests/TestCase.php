<?php

namespace Luceos\Spam\Tests;

use Flarum\Foundation\Config;
use Flarum\Locale\LocaleManager;
use Flarum\Locale\Translator;
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

        $container->singleton(LocaleManager::class, function () {
            $manager = new LocaleManager(new Translator('en'));

            $manager->addLocale('en', 'English');
            $manager->addLocale('nl', 'Nederlands');

            return $manager;
        });
    }
}
