<?php

namespace RotHub\Laravel\Services;

abstract class Service
{
    public static function fake()
    {
        return new static();
    }
}
