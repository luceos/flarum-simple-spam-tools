<?php

namespace Luceos\Spam\Concerns;

use Flarum\Api\Client;
use Flarum\Extension\ExtensionManager;
use Flarum\User\User;
use FoF\Spamblock\Controllers\MarkAsSpammerController;

trait SpamBlock
{
    use Users;

    protected function markAsSpammer(User $user)
    {
        /** @var ExtensionManager $extensions */
        $extensions = resolve(ExtensionManager::class);

        if ($extensions->isEnabled('fof-spamblock')) {
            /** @var Client $api */
            $api = resolve(Client::class);

            $api->send(
                MarkAsSpammerController::class,
                $this->getModerator(),
                ['id' => $user->id]
            );
        }
    }
}
