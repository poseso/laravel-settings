<?php

namespace Poseso\Settings\Cache;

use Poseso\Settings\Events\PropertyRemoved;
use Poseso\Settings\Events\PropertyWritten;
use Poseso\Settings\Events\AllSettingsRemoved;

class EventSubscriber
{
    /**
     * The cache instance.
     *
     * @var \Poseso\Settings\Cache\Cache
     */
    protected $cache;

    /**
     * EventListener constructor.
     *
     * @param \Poseso\Settings\Cache\Cache $cache
     * @return void
     */
    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * The PropertyWritten event handler.
     *
     * @param \Poseso\Settings\Events\PropertyWritten $event
     * @return void
     */
    public function propertyWritten(PropertyWritten $event)
    {
        if ($event->getScope() === '') {
            $this->cache->set($event->key, $event->value);
        }
    }

    /**
     * The PropertyRemoved event handler.
     *
     * @param \Poseso\Settings\Events\PropertyRemoved $event
     * @return void
     */
    public function propertyRemoved(PropertyRemoved $event)
    {
        if ($event->getScope() === '') {
            $this->cache->forget($event->key);
        }
    }

    /**
     * The AllSettingsRemoved event handler.
     *
     * @param \Poseso\Settings\Events\AllSettingsRemoved $event
     * @return void
     */
    public function allSettingsRemoved(AllSettingsRemoved $event)
    {
        if ($event->getScope() === '') {
            $this->cache->flush();
        }
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  \Illuminate\Events\Dispatcher $events
     * @return void
     */
    public function subscribe($events)
    {
        $events->listen(PropertyWritten::class, [$this, 'propertyWritten']);
        $events->listen(PropertyRemoved::class, [$this, 'propertyRemoved']);
        $events->listen(AllSettingsRemoved::class, [$this, 'allSettingsRemoved']);
    }
}
