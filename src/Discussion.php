<?php

namespace Luceos\Spam;

use Flarum\Extend\ExtenderInterface;
use Flarum\Extension\Extension;
use Flarum\Post\Event\Saving;
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
            if ($event->post->number !== null || $event->post->number !== 1) return;

            $discussion = $event->post->discussion;
            $firstPost = $event->post;

            // Disallow any blocked content and any urls in subject.
            $badContent = $this->containsProblematicContent($discussion->title)
                || $this->validateUrl('url', $discussion->title);

            if ($badContent
                // Ignore discussions that are soft deleted (already).
                && $discussion->hidden_at === null
                // Only enact spam prevent on fresh users.
                && $this->isFreshUser($discussion->user)) {

                $discussion->afterSave(function (\Flarum\Discussion\Discussion $discussion) use ($firstPost) {
                    // Try to mark as spammer
                    $this->markAsSpammer($discussion->user)
                        // otherwise mark for approval
                        || $this->unapproveAndFlag($firstPost, 'Discussion subject contains bad content.');
                });
            }
        });
    }
}
