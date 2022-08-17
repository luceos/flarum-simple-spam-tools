<?php

namespace Luceos\Spam;

use Flarum\Extend\ExtenderInterface;
use Flarum\Extension\Extension;
use Illuminate\Contracts\Container\Container;

class Filter implements ExtenderInterface
{
    public static array $acceptableDomains = [];
    public static ?int $userPostCount = null;
    public static ?int $userAge = null;
    public static ?int $moderatorUserId = null;

    public function allowLinksFromDomain(string $domain)
    {
        static::$acceptableDomains[] = parse_url($domain, PHP_URL_HOST);

        return $this;
    }

    public function checkForUserUpToPostContribution(int $posts = 1)
    {
        static::$userPostCount = $posts;

        return $this;
    }

    public function checkForUserUpToHoursSinceSignUp(int $hours = 1)
    {
        static::$userAge = $hours;

        return $this;
    }

    public function moderateAsUser(int $userId)
    {
        static::$moderatorUserId = $userId;

        return $this;
    }

    public function extend(Container $container, Extension $extension = null)
    {

    }
}
