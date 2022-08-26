<?php

namespace Luceos\Spam\Concerns;

use Luceos\Spam\Filter;

trait Content
{
    public function containsProblematicContent(string $content): bool
    {
        $domains = Filter::getAcceptableDomains();
        
        $domains = join('|', $domains);
        $domains = str_replace('.', '\.', $domains);
        return
            // phone
            preg_match('~(\+|00)[0-9 ]{9,}~', $content) ||
            // email
            preg_match('~[\S]+@[\S]+\.[\S]+~', $content) ||
            // links
            preg_match("~https?:\/\/(?!([^\.]+.)?($domains))([-\w.]+)~", $content);
    }
}
