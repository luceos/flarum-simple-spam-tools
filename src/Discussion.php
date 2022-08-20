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
    use Concerns\Approval,
        Concerns\Content,
        Concerns\Users,
        Concerns\SpamBlock,
        ValidatesAttributes;

    public function extend(Container $container, Extension $extension = null)
    {
        /** @var Dispatcher $events */
        $events = $container->make(Dispatcher::class);

        $events->listen(Saving::class, function (Saving $event) {
            // Disallow any blocked content and any urls in subject.
            $badContent = $this->containsProblematicContent($event->discussion->title)
                || $this->validateUrl('url', $event->discussion->title);

            if ($badContent
                // Ignore discussions that are soft deleted (already).
                && $event->discussion->hidden_at === null
                // Only enact spam prevent on fresh users.
                && $this->isFreshUser($event->discussion->user)) {

                $event->discussion->afterSave(function (\Flarum\Discussion\Discussion $discussion) {
                    // Try to mark as spammer
                    $this->markAsSpammer($discussion->user)
                        // otherwise mark for approval
                        || $this->unapproveAndFlag($discussion->firstPost, 'Discussion subject contains bad content.');
                });
            }
        });
    }
}
