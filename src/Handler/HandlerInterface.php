<?php


namespace App\Handler;



interface HandlerInterface
{
    /**
     * @param object $object
     * @return mixed
     */
    public function handle(object $object);
}