<?php

namespace Flarum\Foundation;

use Flarum\User\User;
use Illuminate\Contracts\Events\Dispatcher;
use LogicException;

trait DispatchEventsTrait
{
    /**
     * @var Dispatcher
     */
    protected $events;

    /**
     * 分发实体相关的所有事件
     * 
     * @param object $entity 需要分发事件的实体
     * @param User|null $actor 执行操作的当前用户
     */
    public function dispatchEventsFor($entity, ?User $actor = null): void
    {
        foreach ($entity->releaseEvents() as $event) {
            $this->injectActorToEvent($event, $actor);
            $this->events->dispatch($event);
        }
    }

    /**
     * 安全地注入 Actor 到事件对象
     * 
     * @param object $event 事件对象
     * @param User|null $actor 需要注入的用户
     * 
     * @throws LogicException 当事件不兼容时抛出异常
     */
    protected function injectActorToEvent(object $event, ?User $actor): void
    {
        // 优先使用类型安全的方法注入
        if ($event instanceof ActorAwareEventInterface) {
            $event->setActor($actor);
            return;
        }

        // 兼容旧版事件的属性注入（带类型检查）
        if (property_exists($event, 'actor')) {
            if (!$event->actor instanceof User && null !== $event->actor) {
                throw new LogicException(
                    sprintf('Event %s has non-user type for actor property', get_class($event))
                );
            }
            
            $event->actor = $actor;
            return;
        }

        // 调试模式下的警告记录
        if (getenv('FLARUM_DEBUG')) {
            error_log(
                sprintf('[Deprecation] Event %s does not implement actor injection interface', get_class($event))
            );
        }
    }
}

// 配套接口定义
interface ActorAwareEventInterface
{
    /**
     * 设置事件关联的操作用户
     * 
     * @param User|null $actor
     */
    public function setActor(?User $actor): void;
}