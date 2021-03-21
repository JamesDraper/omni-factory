<?php
declare(strict_types=1);

namespace Test\Res;

use OmniFactory\Factory;

class BoundClass implements InterfaceWithConstant
{
    private StaticMethodDependencies $staticMethodDependencies;

    public static function create(Factory $factory): self
    {
        return new self(
            $factory(StaticMethodDependencies::class)
        );
    }

    public function __construct(StaticMethodDependencies $staticMethodDependencies)
    {
        $this->staticMethodDependencies = $staticMethodDependencies;
    }

    public function getStaticMethodDependencies(): StaticMethodDependencies
    {
        return $this->staticMethodDependencies;
    }
}
