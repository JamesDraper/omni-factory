<?php
declare(strict_types=1);

namespace OmniFactory;

use LogicException;

use function interface_exists;
use function call_user_func;
use function is_callable;
use function method_exists;
use function class_exists;
use function constant;
use function vsprintf;
use function defined;

class Factory
{
    private array $objs = [];

    private array $aliases;

    private string $method;

    private string $const;

    public static function create(callable $func): self
    {
        call_user_func($func, $config = new Config);

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

            case $hasConst && $hasMethod:
                throw $this->createResolutionException($pathOrAlias, 'found both.');

            default:
                if (!interface_exists($pathOrAlias) && !class_exists($pathOrAlias)) {
                    throw new LogicException(vsprintf('Class or interface not found: %s.', [
                        $pathOrAlias,
                    ]));
                }

                throw $this->createResolutionException($pathOrAlias, 'found neither.');
        }
    }

    private function __construct(array $aliases, string $method, string $const)
    {
        $this->aliases = $aliases;
        $this->method  = $method;
        $this->const   = $const;
    }

    private function createResolutionException(string $path, string $suffix): LogicException
    {
        $message = vsprintf(
            'expected %s to be either a class with the public constant %s or the public static method %s ',
            [$path, $this->const, $this->method]
        );

        return new LogicException($message . $suffix);
    }
}
