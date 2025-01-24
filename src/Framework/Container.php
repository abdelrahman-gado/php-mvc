<?php

declare(strict_types=1);

namespace Framework;

use Closure;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionNamedType;

class Container
{
    private array $registry = [];

    public function set(string $name, Closure $value): void
    {
        $this->registry[$name] = $value;
    }

    public function get(string $className): object
    {
        if (array_key_exists($className, $this->registry)) {
            return $this->registry[$className]();
        }

        $dependencies = [];
        $reflector = new ReflectionClass($className);
        $constructor = $reflector->getConstructor();
        if (!$constructor) {
            return new $className;
        }

        foreach ($constructor->getParameters() as $parameter) {
            $type = $parameter->getType();

            if ($type === null) {
                throw new InvalidArgumentException("Constructor parameter '{$parameter->getName()}' 
                in the $className class has no type declaration");
            }

            if (!($type instanceof ReflectionNamedType)) {
                throw new InvalidArgumentException("Constructor parameter '{$parameter->getName()}' 
                in the $className class is an invalid type: '$type' -
                only single named types are supported");
            }

            if ($type->isBuiltIn()) {
                throw new InvalidArgumentException("Unable to resolve constructor parameter {$parameter->getName()} 
                of type '$type' in the $className");
            }

            $dependencies[] = $this->get((string) $type);
        }

        return new $className(...$dependencies);
    }
}