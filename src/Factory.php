<?php
declare(strict_types=1);

namespace OmniFactory;

use InvalidArgumentException;
use LogicException;
use Throwable;

use function interface_exists;
use function call_user_func;
use function class_exists;
use function is_callable;
use function array_walk;
use function constant;
use function vsprintf;
use function defined;

class Factory
{
    private array $objs = [];

    private array $aliases;

    private string $method;

    private string $const;

    public static function create(?callable $func = null): self
    {
        $config = new Config;

        if ($func) {
            call_user_func($func, $config);
        }

        return new self(
            $config->getAliases(),
            $config->getMethod(),
            $config->getConst(),
        );
    }

    public function __invoke(string $pathOrAlias): mixed
    {
        if (isset($this->objs[$pathOrAlias])) {
            return $this->objs[$pathOrAlias];
        }

        if (isset($this->aliases[$pathOrAlias])) {
            $pathOrAlias = $this->aliases[$pathOrAlias];
        }

        switch (true) {
            case $hasMethod = is_callable([$pathOrAlias, $this->method]):
                return $this->objs[$pathOrAlias] = call_user_func([$pathOrAlias, $this->method], $this);

            case $hasConst = @defined($pathOrAlias . '::' . $this->const):
                $pathOrAlias = constant($pathOrAlias . '::' . $this->const);

                return $this->objs[$pathOrAlias] = $this($pathOrAlias);

            default:
                if (!interface_exists($pathOrAlias) && !class_exists($pathOrAlias)) {
                    throw new LogicException(vsprintf('Class or interface not found: %s.', [
                        $pathOrAlias,
                    ]));
                }

                throw new LogicException(vsprintf(
                    'Expected %s to be either a class with the public constant '
                        . '%s or the public static method %s but found neither.',
                    [$pathOrAlias, $this->const, $this->method],
                ));
        }
    }

    private function __construct(array $aliases, string $method, string $const)
    {
        $this->aliases = $aliases;
        $this->method  = $method;
        $this->const   = $const;
    }
}

class Config
{
    private string $const = 'CREATED_BY';

    private string $method = 'create';

    private array $aliases = [];

    public function setConst(string $const): self
    {
        $this->const = $const;

        return $this;
    }

    public function getConst(): string
    {
        return $this->const;
    }

    public function setMethod(string $method): self
    {
        $this->method = $method;

        return $this;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function setAliases(array $aliases): self
    {
        $this->aliases = [];

        return $this->addAliases($aliases);
    }

    public function addAliases(array $aliases): self
    {
        try {
            array_walk($aliases, fn($path, $alias) => $this->addAlias($alias, $path));
        } catch (Throwable $e) {
            throw new InvalidArgumentException(
                '$aliases must be an associative array mapping aliases to class paths.'
            );
        }

        return $this;
    }

    public function addAlias(string $alias, string $path): self
    {
        $this->aliases[$alias] = $path;

        return $this;
    }

    public function getAliases(): array
    {
        return $this->aliases;
    }
}
