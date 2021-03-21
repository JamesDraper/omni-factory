<?php
declare(strict_types=1);

namespace Test\Res;

interface InterfaceWithConstant
{
    public const CREATED_BY = BoundClass::class;
}
