<?php
declare(strict_types=1);

namespace Test\Res;

class StaticMethodNoDependencies
{
    public static function create(): self
    {
        return new self;
    }
}
