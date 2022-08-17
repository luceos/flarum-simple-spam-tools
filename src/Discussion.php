<?php

namespace Luceos\Spam;

use Flarum\Discussion\Event\Saving;
use Flarum\Extend\ExtenderInterface;
use Flarum\Extension\Extension;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Validation\Concerns\ValidatesAttributes;

class Discussion implements ExtenderInterface
{
    use Concerns\Users,
        Concerns\SpamBlock,
        ValidatesAttributes;

    public function extend(Container $container, Extension $extension = null)
    {
        /** @var Dispatcher $events */
        $events = $container->make(Dispatcher::class);

        $events->listen(Saving::class, function (Saving $event) {
            // We do not allow useless discussions that have a URL.
            if ($this->validateUrl('url', $event->discussion->title)
                && $event->discussion->hidden_at === null
                && $this->isFreshUser($event->discussion->user)) {

                $event->discussion->afterSave(function (\Flarum\Discussion\Discussion $discussion) {
                    $this->markAsSpammer($discussion->user);
                });
            }
        });
    }
}
