<?php

/*
 * This file is part of Flarum.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace Flarum\Likes\Listener;

use Flarum\Api\Controller;
use Flarum\Api\Event\WillGetData;
use Illuminate\Contracts\Events\Dispatcher;

class AddPostLikesRelationship
{
    /**
     * @param Dispatcher $events
     */
    public function subscribe(Dispatcher $events)
    {
        $events->listen(WillGetData::class, [$this, 'includeLikes']);
    }

    /**
     * @param WillGetData $event
     */
    public function includeLikes(WillGetData $event)
    {
        if ($event->isController(Controller\ShowDiscussionController::class)) {
            $event->addInclude('posts.likes');
        }

        if ($event->isController(Controller\ListPostsController::class)
            || $event->isController(Controller\ShowPostController::class)
            || $event->isController(Controller\CreatePostController::class)
            || $event->isController(Controller\UpdatePostController::class)) {
            $event->addInclude('likes');
        }
    }
}
