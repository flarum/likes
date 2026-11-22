<?php

/*
 * This file is part of Flarum.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace Flarum\Likes\Event;

use Flarum\Post\Post;
use Flarum\User\User;

class PostWasLiked
{
    /**
     * @var Post
     */
    public $post;

    /**
     * @var User
     */
    public $user;

    /**
     * @var User
     */
    public $actor;

    /**
     * @param Post $post
     * @param User $user
     * @param User $actor
     */
    public function __construct(Post $post, User $user, User $actor)
    {
        $this->post = $post;
        $this->user = $user;
        $this->actor = $actor;
    }
}
