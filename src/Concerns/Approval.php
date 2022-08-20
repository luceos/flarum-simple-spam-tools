<?php

namespace Luceos\Spam\Concerns;

use Flarum\Extension\ExtensionManager;
use Flarum\Flags\Flag;
use Flarum\Post\Post;
use Luceos\Spam\Filter;

trait Approval
{
    use Users;

    protected function requireApproval(Post $post, string $reason = null)
    {
        /** @var ExtensionManager $extensions */
        $extensions = resolve(ExtensionManager::class);

        if ($extensions->isEnabled('flarum-approval')) {
            $post->is_approved = false;

            $post->afterSave(function (Post $post) use ($reason, $extensions) {
                $this->unapproveAndFlag($post, $reason);
            });

            return true;
        }

        return false;
    }

    protected function unapproveAndFlag(Post $post, string $reason = null)
    {
        /** @var ExtensionManager $extensions */
        $extensions = resolve(ExtensionManager::class);

        if (! $extensions->isEnabled('flarum-approval')) return false;

        if ($post->number === 1) {
            $post->discussion->is_approved = false;
            $post->discussion->save();
        }

        if ($extensions->isEnabled('flarum-flags')) {
            $flag = new Flag;

            $flag->post_id = $post->id;
            $flag->type = 'approval';
            $flag->reason = 'Blocked by discuss';
            $flag->reason_detail = $reason;
            $flag->user()->associate($this->getModerator());
            $flag->created_at = time();

            $flag->save();
        }

        return true;
    }
}
