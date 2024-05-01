<?php

namespace packages\notifications;

use packages\base\EventInterface;

interface IChannel
{
    public function notify(EventInterface $e): void;

    public function canNotify(EventInterface $e): bool;

    public function getName(): string;
}
