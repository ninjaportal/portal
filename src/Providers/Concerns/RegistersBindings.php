<?php

namespace NinjaPortal\Portal\Providers\Concerns;

use InvalidArgumentException;

trait RegistersBindings
{
    protected function registerRepositories(): void
    {
        foreach ($this->repositoryBindings ?? [] as $binding) {
            $interface = $binding['interface'];
            $implementation = $binding['implementation'];
            $modelDefinition = $binding['model'] ?? null;

            $this->app->bind($interface, function ($app) use ($implementation, $modelDefinition) {
                if ($modelDefinition === null) {
                    return new $implementation;
                }

                $modelInstance = $this->resolveModelBinding($modelDefinition);

                return new $implementation($modelInstance);
            });
        }
    }

    protected function registerServices(): void
    {
        foreach ($this->serviceBindings ?? [] as $interface => $concrete) {
            $this->app->singleton($interface, $concrete);
        }
    }

    protected function resolveModelBinding($definition): object
    {
        if (is_array($definition)) {
            foreach ($definition as $candidate) {
                try {
                    return $this->resolveModelBinding($candidate);
                } catch (\Throwable) {
                    continue;
                }
            }

            throw new InvalidArgumentException('Unable to resolve repository model from array definition.');
        }

        if (is_callable($definition)) {
            $definition = $definition($this->app);
        }

        if (is_string($definition) && str_starts_with($definition, 'config:')) {
            $definition = config(substr($definition, 7));
        }

        if (is_string($definition)) {
            return new $definition;
        }

        if (is_object($definition)) {
            return $definition;
        }

        throw new InvalidArgumentException('Invalid repository model definition.');
    }
}
