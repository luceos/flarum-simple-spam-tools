<?php

namespace Luceos\Spam\Concerns;

use Flarum\Locale\LocaleManager;
use LanguageDetection\Language;
use Luceos\Spam\Filter;

trait Content
{
    public function containsProblematicContent(string $content): bool
    {
        return $this->containsProblematicLinks($content)
            || $this->containsAlternateLanguage($content);
    }

    public function containsProblematicLinks(string $content): bool
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

    public function containsAlternateLanguage(string $content): bool
    {
        // strip links
        $content = preg_replace('~[\S]+@[\S]+\.[\S]+~', '', $content);
        $content = preg_replace('~https?:\/\/([-\w.]+)~', '', $content);
        $content = preg_replace('~(\+|00)[0-9 ]{9,}~', '', $content);


        // Let's not do language analysis on short strings.
        if (mb_strlen($content) < 10) return false;

        /** @var LocaleManager $locales */
        $locales = resolve(LocaleManager::class);

        $locales = array_keys($locales->getLocales());

        $languageDetection = (new Language)->detect($content);

        return ! empty($languageDetection) && ! in_array((string) $languageDetection, $locales);
    }
}
