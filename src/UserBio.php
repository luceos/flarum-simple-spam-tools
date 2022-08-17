<?php

namespace Luceos\Spam;

use FoF\UserBio\Event\BioChanged;
use Flarum\Extend\ExtenderInterface;
use Flarum\Extension\Extension;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;

class UserBio implements ExtenderInterface
{
    use Concerns\Users,
        Concerns\Content;

    public function extend(Container $container, Extension $extension = null)
    {
        /** @var Dispatcher $events */
        $events = $container->make(Dispatcher::class);

        $events->listen(BioChanged::class, function (BioChanged $event) {
            if(
                $event->actor->is($event->user)
                && $this->isFreshUser($event->user)
                && $this->containsProblematicContent($event->user->bio)
            ) {
                $user = $event->user;

                $originalBio = $user->getOriginal('bio');

                if (! $this->containsProblematicContent($originalBio)) {
                    $user->bio = $originalBio;
                } else {
                    $user->bio = '[Bio has been auto moderated]';
                }

                $user->isDirty('bio') && $user->save();
            }
        });
    }
}
