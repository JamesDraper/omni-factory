<?php
declare(strict_types=1);

namespace Test;

use LogicException;
use OmniFactory\Factory;
use OmniFactory\Config;

use Test\Res\ClassWithNoMethod;
use Test\Res\StaticMethodNoDependencies;
use Test\Res\StaticMethodDependencies;
use Test\Res\InterfaceWithConstant;
use Test\Res\BoundClass;

use PHPUnit\Framework\TestCase;

class FactoryTest extends TestCase
{
    private Factory $factory;

    /**
     * @test
     */
    public function it_should_return_a_class_instance_by_static_method_with_no_dependencies(): void
    {
        $result = ($this->factory)(StaticMethodNoDependencies::class);

        $this->assertInstanceOf(StaticMethodNoDependencies::class, $result);
    }

    /**
     * @test
     */
    public function it_should_return_a_class_instance_by_static_method_with_dependencies(): void
    {
        $result1 = ($this->factory)(StaticMethodDependencies::class);

        $this->assertInstanceOf(StaticMethodDependencies::class, $result1);

        $result2 = ($this->factory)(StaticMethodNoDependencies::class);
        $result3 = $result1->getStaticMethodNoDependencies();

        $this->assertSame($result2, $result3);
    }

    /**
     * @test
     */
    public function it_should_return_a_class_instance_by_constant(): void
    {
        $result1 = ($this->factory)(InterfaceWithConstant::class);

        $this->assertInstanceOf(BoundClass::class, $result1);

        $result2 = ($this->factory)(StaticMethodDependencies::class);
        $result3 = $result1->getStaticMethodDependencies();

        $this->assertSame($result2, $result3);
    }

    /**
     * @test
     */
    public function it_should_resolve_an_alias(): void
    {
        $result1 = ($this->factory)('interface-with-constant');
        $result2 = ($this->factory)(InterfaceWithConstant::class);

        $this->assertSame($result1, $result2);
    }

    /**
     * @test
     */
    public function it_should_throw_a_logic_exception_if_the_class_has_no_create_method(): void
    {
        $this->expectException(LogicException::class);

        ($this->factory)(ClassWithNoMethod::class);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->factory = Factory::create(function (Config $config): void {
            $config->addAlias('interface-with-constant', InterfaceWithConstant::class);
        });
    }
}
