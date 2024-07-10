<?php

namespace packages\notifications\Listeners;

use packages\base\Log;
use packages\notifications\Events;

class Base
{
    public function packagesLoaded(): void
    {
        $log = Log::getInstance();
        $log->debug("fire 'events' event");
        (new Events())->trigger();
    }
}
