<?php
declare(strict_types=1);

namespace Test\Res;

interface InterfaceMappingToClassWithNoMethodOrConstant
{
    public const CREATED_BY = ClassWithNoMethodOrConstant::class;
}
