<?php
declare(strict_types=1);

namespace OmniFactory;

use OmniFactory\Exception\CreateException;

use function interface_exists;
use function call_user_func;
use function class_exists;
use function is_callable;
use function constant;
use function vsprintf;
use function defined;

final class Factory
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
            $classPath = $this->aliases[$pathOrAlias];
            unset($this->aliases[$pathOrAlias]);

            return $this->objs[$pathOrAlias] = $this($classPath);
        }

        switch (true) {
            case is_callable([$pathOrAlias, $this->method]):
                return $this->objs[$pathOrAlias] = call_user_func([$pathOrAlias, $this->method], $this);

            case @defined($pathOrAlias . '::' . $this->const):
                $pathOrAlias = constant($pathOrAlias . '::' . $this->const);

                return $this->objs[$pathOrAlias] = $this($pathOrAlias);

            default:
                if (!interface_exists($pathOrAlias) && !class_exists($pathOrAlias)) {
                    throw new CreateException(vsprintf('Class or interface not found: %s.', [
                        $pathOrAlias,
                    ]));
                }

                throw new CreateException(vsprintf(
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
