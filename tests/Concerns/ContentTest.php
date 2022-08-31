<?php

namespace Luceos\Spam\Tests\Concerns;

use Luceos\Spam\Concerns\Content;
use Luceos\Spam\Filter;
use Luceos\Spam\Tests\TestCase;

class ContentTest extends TestCase
{
    use Content;

    /**
     * @covers \Luceos\Spam\Concerns\Content::containsProblematicLinks
     * @test
     */
    function allows_reasonable_content()
    {
        $this->assertFalse(
            $this->containsProblematicContent('hello')
        );
        $this->assertFalse(
            $this->containsProblematicContent(<<<EOM
Hi there,

Have some questions.
EOM
)
        );
    }

    /**
     * @covers \Luceos\Spam\Concerns\Content::containsProblematicLinks
     * @test
     */
    function fails_on_link()
    {
        $this->assertTrue(
            $this->containsProblematicContent(
                'https://spamlink.com'
            )
        );
        $this->assertTrue(
            $this->containsProblematicContent(<<<EOM
Hi,

https://spamlink.com is the best!
EOM
)
        );
        $this->assertTrue(
            $this->containsProblematicContent(<<<EOM
Hi,

[this](https://spamlink.com) is the best!
EOM
            )
        );
    }

    /**
     * @covers \Luceos\Spam\Concerns\Content::containsProblematicLinks
     * @test
     */
    function fails_on_emails()
    {
        $this->assertTrue(
            $this->containsProblematicContent(
                'test@gmail.com'
            )
        );
        $this->assertTrue(
            $this->containsProblematicContent(<<<EOM
Hi,

test@gmail.com is the best!
EOM
            )
        );
        $this->assertTrue(
            $this->containsProblematicContent(<<<EOM
Hi,

[this](test@gmail.com) is the best!
EOM
            )
        );
    }

    /**
     * @covers \Luceos\Spam\Concerns\Content::containsProblematicLinks
     * @test
     */
    function allows_links_with_acceptable_domain()
    {
        (new Filter)
            ->allowLinksFromDomain('acceptable-domain.com');

        $this->assertFalse(
            $this->containsProblematicContent(
                'https://acceptable-domain.com'
            )
        );
        $this->assertFalse(
            $this->containsProblematicContent(<<<EOM
Hi,

https://acceptable-domain.com is the best!
EOM
            )
        );
        $this->assertFalse(
            $this->containsProblematicContent(<<<EOM
Hi,

[this](https://acceptable-domain.com) is the best!
EOM
            )
        );
    }

    /**
     * @covers \Luceos\Spam\Concerns\Content::containsProblematicLinks
     * @test
     */
    function fails_on_example_from_discuss_2022_08_26()
    {
        (new Filter)
            ->allowLinksFromDomain('flarum.org')
            ->allowLinksFromDomain('github.com')
            ->allowLinksFromDomain('blomstra.net')
            ->allowLinksFromDomain('extiverse.com')
            ->allowLinksFromDomain('blomstra.community')
            ->allowLinksFromDomain('kilowhat.net')
            ->allowLinksFromDomain('opencollective.org')
            ->allowLinksFromDomain('packagist.com');

        $this->assertTrue(
            $this->containsProblematicContent(
                <<<EOM
If you are looking for the best and most affordable car rental service, book Chandigarh to Delhi taxi at Vahan Seva. We are a renowned and reliable car rental agency in Chandigarh. We are famous for offering the best cab booking services to our clients. You can find various **[Chandigarh to Delhi Taxi Services](https://jkbrothertravels.com/oneway/taxi-service/chandigarh-to-delhi)** available from where you can book your cab from Chandigarh to Delhi. for more information visit our website.
EOM
            )
        );
    }

    /**
     * @test
     * @covers \Luceos\Spam\Concerns\Content::containsAlternateLanguage
     */
    function allows_installed_languages()
    {
        $this->assertFalse(
            $this->containsProblematicContent(
                <<<EOM
I created my profile on August 27th 2015. You won't believe it, but it's true.
EOM
            ), 'Falsely marks English as invalid language'
        );

        // Dutch
        $this->assertFalse(
            $this->containsProblematicContent(
                <<<EOM
Ik heb mijn gebruikersprofiel aangemaakt op 27 augustus 2015. Je zult het niet geloven, maar het is echt waar.
EOM
            ), 'Falsely marks Dutch as invalid language'
        );
    }

    /**
     * @test
     * @covers \Luceos\Spam\Concerns\Content::containsAlternateLanguage
     */
    function fails_for_other_languages()
    {
        // German
        $this->assertTrue(
            $this->containsProblematicContent(
                <<<EOM
Ich habe mein account erstellt am 27er August 2015. Du kannst es bestimmt nicht glauben, aber es ist wirklich war.
EOM
            )
        );

        // Chinese simplified
        $this->assertTrue(
            $this->containsProblematicContent(
                <<<EOM
我在 2015 年 8 月 27 日创建了我的用户资料。你不会相信，但这是真的。
EOM
            )
        );

        // Turkish
        $this->assertTrue(
            $this->containsProblematicContent(
                <<<EOM
27 Ağustos 2015'te kullanıcı profilimi oluşturdum. İnanmayacaksınız ama gerçekten doğru.
EOM
            )
        );
    }
}
