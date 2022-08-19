<?php

namespace Luceos\Spam\Concerns;

use Flarum\Foundation\Config;
use Luceos\Spam\Filter;

trait Content
{
    public function containsProblematicContent(string $content): bool
    {
        /** @var Config $config */
        $config = resolve(Config::class);

        $domains = Filter::$acceptableDomains + [$config->url()->getHost()];
        $domains = join('|', $domains);
        $domains = str_replace('.', '\.', $domains);

        return
            // phone
            preg_match('~(\+|00)[0-9 ]{10,}~', $content) ||
            // email
            preg_match('~[\S]+@[\S]+\.[\S]+~', $content) ||
            // links
            preg_match("~https?:\/\/(?!([^\.]+.)?($domains))([-\w.]+)~", $content);
    }
}
