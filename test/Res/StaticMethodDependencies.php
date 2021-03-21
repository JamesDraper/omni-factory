<?php
declare(strict_types=1);

namespace Test\Res;

use OmniFactory\Factory;

class StaticMethodDependencies
{
    private StaticMethodNoDependencies $staticMethodNoDependencies;

    public static function create(Factory $factory): self
    {
        return new self(
            $factory(StaticMethodNoDependencies::class)
        );
    }

    public function __construct(StaticMethodNoDependencies $staticMethodNoDependencies)
    {
        $this->staticMethodNoDependencies = $staticMethodNoDependencies;
    }

    public function getStaticMethodNoDependencies(): StaticMethodNoDependencies
    {
        return $this->staticMethodNoDependencies;
    }
}
