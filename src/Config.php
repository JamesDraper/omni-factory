<?php
declare(strict_types=1);

namespace OmniFactory;

use OmniFactory\Exception\ConfigException;

use Throwable;

use function array_walk;

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
            throw new ConfigException(
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
