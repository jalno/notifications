<?php

namespace packages\notifications\Events;

use packages\base\Event;
use packages\notifications\API;
use packages\notifications\IChannel;

class Channels extends Event
{
    /**
     * @param class-string<IChannel>|IChannel $channel
     */
    public function add($channel): void
    {
        if (is_string($channel)) {
            API::addChannel(new $channel());
        } elseif ($channel instanceof IChannel) {
            API::addChannel($channel);
        } else {
            throw new \InvalidArgumentException('only string or '.IChannel::class.' type can pass to add()');
        }
    }

    /**
     * @return IChannel[]
     */
    public function get(): array
    {
        return API::getChannels();
    }
}
