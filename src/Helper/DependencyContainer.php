<?php
// /src/Helper/DependencyContainer.php

namespace Web\PublicHtml\Helper;

use Psr\Container\ContainerInterface;

class DependencyContainer implements ContainerInterface
{
    private static ?self $instance = null;
    private array $container = [];
    private array $resolved = [];
    private array $factories = [];
    private array $prototypes = []; // ������Ÿ���� ���� �迭 �߰�

    private function __construct() {}

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function set(string $id, $concrete): void
    {
        $this->container[$id] = $concrete;
        unset($this->resolved[$id]);
    }

    public function addFactory(string $id, callable $factory): void
    {
        $this->factories[$id] = $factory;
        unset($this->resolved[$id]);
    }

    // �׻� ���ο� �ν��Ͻ��� �����ϴ� ������Ÿ�� ��� ��� �޼���
    public function addPrototype(string $id, callable $prototype): void
    {
        $this->prototypes[$id] = $prototype;
    }

    public function get($id)
    {
        if (!$this->has($id)) {
            throw new \Exception("No entry or class found for '$id'");
        }

        // ������Ÿ���̸� �׻� ���ο� �ν��Ͻ��� �����Ͽ� ��ȯ
        if (isset($this->prototypes[$id])) {
            return $this->prototypes[$id]($this);
        }

        if (!isset($this->resolved[$id])) {
            if (isset($this->factories[$id])) {
                $this->resolved[$id] = $this->factories[$id]($this);
            } else {
                $this->resolved[$id] = $this->resolve($this->container[$id]);
            }
        }

        return $this->resolved[$id];
    }

    public function has($id): bool
    {
        return isset($this->container[$id]) || isset($this->factories[$id]) || isset($this->prototypes[$id]);
    }

    private function resolve($concrete)
    {
        if ($concrete instanceof \Closure) {
            return $concrete($this);
        }

        if (is_string($concrete) && class_exists($concrete)) {
            return $this->build($concrete);
        }

        return $concrete;
    }

    private function build($concrete)
    {
        $reflector = new \ReflectionClass($concrete);
        
        if (!$reflector->isInstantiable()) {
            throw new \Exception("Class '$concrete' is not instantiable");
        }

        $constructor = $reflector->getConstructor();
        if (is_null($constructor)) {
            return new $concrete;
        }

        $parameters = $constructor->getParameters();
        $dependencies = $this->resolveDependencies($parameters);

        return $reflector->newInstanceArgs($dependencies);
    }

    private function resolveDependencies(array $parameters): array
    {
        $dependencies = [];

        foreach ($parameters as $parameter) {
            $dependency = $parameter->getClass();
            if ($dependency === null) {
                if ($parameter->isDefaultValueAvailable()) {
                    $dependencies[] = $parameter->getDefaultValue();
                } else {
                    throw new \Exception("Cannot resolve dependency for parameter '{$parameter->getName()}'");
                }
            } else {
                $dependencies[] = $this->get($dependency->name);
            }
        }

        return $dependencies;
    }

    private function __clone() {}
    public function __wakeup() {}
}