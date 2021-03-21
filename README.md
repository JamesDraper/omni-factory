# Omni-Factory

Omni-Factory is a factory for creating services based on static methods.

## Usage

The most basic usage of Omni-Factory is this:

    <?php

    class Some
    {
        public static function create(): self
        {
            return new self;
        }
    }

    $factory = \OmniFactory\Factory::create();

    $someClass = $factory(Some::class);

When invoked, Omni-Factory will attempt to create an instance of the specified
class path and return it. It does this by calling the static method `create` on
any requested class and returning the result.

It will keep any instances it has created and return them if they are requested
again instead of recreating them.

### Specifying a factory method in another class

It is also possible to add a constant to a class/interface specifying another
classpath in which a create method can be found. This is useful for binding
an interface to an implementation:

    <?php

    interface Strategy
    {

        public const CREATED_BY = Implementation::class;

    }

    class Implementation implements Strategy
    {

        public static function create(): self
        {
            return new self;
        }

    }

    $factory = \OmniFactory\Factory::create();

    $someClass = $factory(Strategy::class);

In the above example, `$someClass` will be an instance of `Implementation`.

When creating an instance of a class, Omni-Factory will prioritize the existence
of a `create` method over the existence of a `CREATED_BY` constant. This is
because when an interface contains a constant specifying an implementing class,
the implementing class will contain both the `CREATED_BY` constant and a
`create` static method.

### Creating an instance with dependencies

If an instance has dependencies, then those dependencies can also be initialized
and injected. When a `create` method is called, The calling instance of
`\OmniFactory\Factory` is passed into the method. Meaning it can be used to
create and return other object instances:

    <?php

    class ClassA
    {
        public static function create(): self
        {
            return new self;
        }
    }

    class ClassB
    {
        public static function create(\OmniFactory\Factory $factory): self
        {
            return new self(
                $factory(ClassA::class),
            );
        }

        public function __construct(
            private ClassA $classA
        ) {}
    }

    $factory = \OmniFactory\Factory::create();

    $someClass = $factory(ClassB::class);

### Changing the method or constant name

By default, Omni-Factory will look for static methods named `create`, or public
constants named `CREATED_BY`. These are default values, but neither are fixed.
When `\OmniFactory\Factory::create()` is called it accepts an optional
callable. A configuration object is passed into that callable. That object can
be used to override the default values.

    <?php

    $factory = \OmniFactory\Factory::create(function (\OmniFactory\Config $config) {
        $config
            ->setConst('BUILT_BY')
            ->setMethod('build')
    });

### Adding aliases

Aliases can also be set when the factory is being configured. Ideally, this
should be kept to a minimum:

    <?php

    $factory = \OmniFactory\Factory::create(function (\OmniFactory\Config $config) {
        $config
            ->addAlias('alias', 'Some\\Class') // Add a single alias
            ->addAliases([ // Add a group of aliases
                'another_alias'     => 'Some\\Other\\Class',
                'yet_another_alias' => 'Yet\\Another\\Class',
            ])
            ->setAliases([ // Replace all aliases
                'and_another_alias'     => 'And\\Some\\Other\\Class',
                'and_yet_another_alias' => 'And\\Yet\\Another\\Class',
            ]);
    });
