<?php

namespace packages\notifications;

use packages\base\EventInterface;
use packages\base\Log;
use packages\base\Packages;

class API
{
    /**
     * @var array<class-string<Notifiable>>
     */
    private static $events = [];

    /**
     * @var IChannel[]
     */
    private static $channels = [];

    /**
     * @var bool
     */
    private static $channelsTriggered = false;

    /**
     * @param class-string<Notifiable> $event
     */
    public static function addEvent(string $event): void
    {
        $log = Log::getInstance();
        if (!class_exists($event)) {
            throw new \InvalidArgumentException($event.' does not exists');
        }
        if (!is_subclass_of($event, Notifiable::class, true)) {
            throw new \InvalidArgumentException($event." didn't implements ".Notifiable::class);
        }

        /**
         * @var class-string<Notifiable>
         */
        $event = strtolower($event);

        $log->debug('search for duplicate');
        if (in_array(self::$events, self::$events)) {
            $log->reply()->error('found');

            return;
        }
        $log->reply('notfound');
        $log->debug('add listener to notifications package');
        $package = Packages::package('notifications');
        if ($package) {
            $package->addEvent($event, Listeners\Events::class.'@handle');
        }
        self::$events[] = $event;
    }

    /**
     * @return array<class-string<Notifiable>>
     */
    public static function getEvents(): array
    {
        return self::$events;
    }

    public static function addChannel(IChannel $channel): void
    {
        self::$channels[] = $channel;
    }

    /**
     * @return IChannel[]
     */
    public static function getChannels(): array
    {
        if (!self::$channels and !self::$channelsTriggered) {
            self::$channelsTriggered = true;
            $event = new Events\Channels();
            $event->trigger();
        }

        return self::$channels;
    }

    /**
     * @param IChannel[]|null $channels
     */
    public static function notify(EventInterface $event, ?array $channels = null): void
    {
        $log = Log::getInstance();
        $name = strtolower(get_class($event));
        $log->info('notify event: ', $name);
        $log->debug('check event is exists in getEvents() array?');
        if (!in_array($name, self::getEvents())) {
            $log->reply('not exists');

            return;
        }
        if (null === $channels) {
            $log->reply('is exists, get channels...');
            $channels = self::getChannels();
        } else {
            $log->reply('is exists, use passed channels...');
        }
        foreach ($channels as $channel) {
            $log->debug('Check for channel that can notify this event:', $channel->getName());
            if (!$channel->canNotify($event)) {
                $log->reply("Can't, skiped");
                continue;
            }
            $log->reply('It can, notifying');

            try {
                $channel->notify($event);
                $log->reply('done');
            } catch (\Exception $e) {
                $log->reply()->error('an exception occured:', $e->__toString());
            }
        }
    }
}
