<?php

namespace App\Handler;

interface HandlerInterface
{
    /**
     * @return mixed
     */
    public function handle(object $object);
}
