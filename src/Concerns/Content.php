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
        /** @var LocaleManager $locales */
        $locales = resolve(LocaleManager::class);

        $locales = array_keys($locales->getLocales());

        $languageDetection = (new Language)
            ->detect($content)
            // Remove installed locales from the resultset for identification.
            ->blacklist($locales)
            // Only retrieve hits that have a high match.
            ->bestResults()
            // Only retrieve the top 1
            ->limit(0, 1)
            // Close detection
            ->close();

        return count($languageDetection) > 0;
    }
}
