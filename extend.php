<?php

/*
 * This file is part of Flarum.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

use Flarum\Api\Serializer\BasicUserSerializer;
use Flarum\Api\Serializer\PostSerializer;
use Flarum\Extend;
use Flarum\Likes\Event\PostWasLiked;
use Flarum\Likes\Event\PostWasUnliked;
use Flarum\Likes\Listener;
use Flarum\Likes\Notification\PostLikedBlueprint;
use Flarum\Post\Post;
use Flarum\User\User;
use Illuminate\Contracts\Events\Dispatcher;

return [
    (new Extend\Frontend('forum'))
        ->js(__DIR__.'/js/dist/forum.js')
        ->css(__DIR__.'/less/forum.less'),

    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js'),

    (new Extend\Model(Post::class))
        ->belongsToMany('likes', User::class, 'post_likes', 'post_id', 'user_id'),

    new Extend\Locales(__DIR__.'/locale'),

    (new Extend\Notification())
        ->type(PostLikedBlueprint::class, PostSerializer::class, ['alert']),

    (new Extend\ApiSerializer(PostSerializer::class))
        ->hasMany('likes', BasicUserSerializer::class)
        ->attribute('canLike', function (array $attributes, $model, PostSerializer $serializer) {
            return (bool) $serializer->getActor()->can('like', $model);
        }),

    (new Extend\Event())
        ->listen(PostWasLiked::class, Listener\SendNotificationWhenPostIsLiked::class)
        ->listen(PostWasUnliked::class, Listener\SendNotificationWhenPostIsUnliked::class),

    function (Dispatcher $events) {
        $events->subscribe(Listener\AddPostLikesRelationship::class);
        $events->subscribe(Listener\SaveLikesToDatabase::class);
    },
];
