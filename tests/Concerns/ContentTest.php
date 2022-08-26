<?php

namespace Luceos\Spam\Tests\Concerns;

use Luceos\Spam\Concerns\Content;
use Luceos\Spam\Filter;
use Luceos\Spam\Tests\TestCase;

class ContentTest extends TestCase
{
    use Content;

    /**
     * @covers \Luceos\Spam\Concerns\Content
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
     * @covers \Luceos\Spam\Concerns\Content
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
     * @covers \Luceos\Spam\Concerns\Content
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
     * @covers \Luceos\Spam\Concerns\Content
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

    function fails_on_example_1()
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
}
