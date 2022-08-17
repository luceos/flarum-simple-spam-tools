<?php

namespace Luceos\Spam\Concerns;

use Flarum\Group\Group;
use Flarum\User\User;
use Luceos\Spam\Filter;

trait Users
{
    public function isFreshUser(User $user): bool
    {
        $age = Filter::$userAge;
        $postsRequired = Filter::$userPostCount;

        if ($age && ($user->isGuest() || $user->joined_at->diffInHours() <= $age)) {
            return true;
        }

        if ($postsRequired && $user->posts->count() <= $postsRequired) {
            return true;
        }

        return false;
    }

    public function getModerator(): User
    {
        if ($moderatorId = Filter::$moderatorUserId) {
            return User::find($moderatorId);
        }

        return User::whereHas('groups', function ($query) {
            $query->where('id', Group::ADMINISTRATOR_ID);
        })->first();
    }
}
