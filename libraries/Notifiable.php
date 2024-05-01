<?php

namespace packages\notifications;

interface Notifiable
{
    public static function getName(): string;

    /**
     * @return string[]
     */
    public static function getParameters(): array;

    /**
     * @return array<string,mixed>
     */
    public function getArguments(): array;

    /**
     * @return \packages\userpanel\User[]
     */
    public function getTargetUsers(): array;
}
