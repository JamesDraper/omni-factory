<?php
declare(strict_types=1);

namespace Test;

use OmniFactory\Config;

use PHPUnit\Framework\TestCase;

use InvalidArgumentException;

class ConfigTest extends TestCase
{
    private Config $config;

    /**
     * @test
     */
    public function it_should_return_a_default_method(): void
    {
        $result = $this
            ->config
            ->getMethod();

        $this->assertSame('create', $result);
    }

    /**
     * @test
     */
    public function it_should_return_self_when_setting_a_method(): void
    {
        $result = $this
            ->config
            ->setMethod('METHOD');

        $this->assertSame($this->config, $result);
    }

    /**
     * @test
     */
    public function it_should_set_a_method(): void
    {
        $result = $this
            ->config
            ->setMethod('METHOD')
            ->getMethod();

        $this->assertSame('METHOD', $result);
    }

    /**
     * @test
     */
    public function it_should_return_a_default_const(): void
    {
        $result = $this
            ->config
            ->getConst();

        $this->assertSame('CREATED_BY', $result);
    }

    /**
     * @test
     */
    public function it_should_return_self_when_setting_a_const(): void
    {
        $result = $this
            ->config
            ->setConst('CONST');

        $this->assertSame($this->config, $result);
    }

    /**
     * @test
     */
    public function it_should_set_a_const(): void
    {
        $result = $this
            ->config
            ->setConst('CONST')
            ->getConst();

        $this->assertSame('CONST', $result);
    }

    /**
     * @test
     */
    public function it_should_return_no_aliases_by_default(): void
    {
        $result = $this
            ->config
            ->getAliases();

        $this->assertSame([], $result);
    }

    /**
     * @test
     */
    public function it_should_return_self_when_adding_an_alias(): void
    {
        $result = $this
            ->config
            ->addAlias('ALIAS', 'PATH');

        $this->assertSame($this->config, $result);
    }

    /**
     * @test
     */
    public function it_should_add_an_alias(): void
    {
        $result = $this
            ->config
            ->addAlias('ALIAS', 'PATH')
            ->getAliases();

        $this->assertSame(['ALIAS' => 'PATH'], $result);
    }

    /**
     * @test
     */
    public function it_should_add_an_alias_that_already_exists(): void
    {
        $result = $this
            ->config
            ->addAlias('ALIAS', 'PATH_1')
            ->addAlias('ALIAS', 'PATH_2')
            ->getAliases();

        $this->assertSame(['ALIAS' => 'PATH_2'], $result);
    }

    /**
     * @test
     */
    public function it_should_return_self_when_adding_aliases(): void
    {
        $result = $this
            ->config
            ->addAliases([
                'A' => 'B',
                'C' => 'D'
            ]);

        $this->assertSame($this->config, $result);
    }

    /**
     * @test
     */
    public function it_should_add_aliases(): void
    {
        $result = $this
            ->config
            ->addAliases([
                'A' => 'B',
                'C' => 'D'
            ])
            ->getAliases();

        $this->assertSame([
            'A' => 'B',
            'C' => 'D'
        ], $result);
    }

    /**
     * @test
     */
    public function it_should_throw_an_exception_when_addAliases_is_passed_an_array(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->config->addAliases(['A', 'B']);
    }

    /**
     * @test
     */
    public function it_should_override_aliases_that_already_exist_when_addAlias_is_called(): void
    {
        $result = $this
            ->config
            ->addAliases([
                'A' => 'B',
                'C' => 'D',
            ])
            ->addAliases([
                'C' => 'E',
            ])
            ->getAliases();

        $this->assertSame([
            'A' => 'B',
            'C' => 'E',
        ], $result);
    }

    /**
     * @test
     */
    public function it_should_return_self_when_setting_aliases(): void
    {
        $result = $this
            ->config
            ->setAliases([
                'A' => 'B',
                'C' => 'D',
            ]);

        $this->assertSame($this->config, $result);
    }

    /**
     * @test
     */
    public function it_should_set_aliases(): void
    {
        $result = $this
            ->config
            ->setAliases([
                'A' => 'B',
                'C' => 'D',
            ])
            ->getAliases();

        $this->assertSame([
            'A' => 'B',
            'C' => 'D',
        ], $result);
    }

    /**
     * @test
     */
    public function it_should_throw_an_exception_when_setAliases_is_passed_an_array(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->config->setAliases(['A', 'B']);
    }

    /**
     * @test
     */
    public function it_should_override_all_aliases_when_setAlias_is_called(): void
    {
        $result = $this
            ->config
            ->addAliases([
                'A' => 'B',
                'C' => 'D',
            ])
            ->setAliases([
                'E' => 'F',
                'G' => 'H',
            ])
            ->getAliases();

        $this->assertSame([
            'E' => 'F',
            'G' => 'H',
        ], $result);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->config = new Config;
    }
}
