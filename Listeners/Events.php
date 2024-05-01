<?php

namespace packages\notifications\Listeners;

use packages\base\EventInterface;
use packages\notifications\API;

class Events
{
    public function handle(EventInterface $e): void
    {
        API::notify($e);
    }
}
