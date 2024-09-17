<?php
namespace Web\PublicHtml\Core;

use Psr\Container\ContainerInterface;

class DependencyContainer implements ContainerInterface
{
    private static ?self $instance = null;
    private array $container = [];
    private array $resolved = [];
    private array $factories = [];
    private array $prototypes = [];

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

    public function addPrototype(string $id, callable $prototype): void
    {
        $this->prototypes[$id] = $prototype;
    }

    public function get($id)
    {
        //$id = $this->resolveNamespace($id); // 자동 등록시 사용

        if (!$this->has($id)) {
            throw new \Exception("No entry or class found for '$id'");
        }

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
            $type = $parameter->getType();
            if ($type instanceof \ReflectionNamedType && !$type->isBuiltin()) {
                $dependencies[] = $this->get($type->getName());
            } elseif ($parameter->isDefaultValueAvailable()) {
                $dependencies[] = $parameter->getDefaultValue();
            } else {
                throw new \Exception("Cannot resolve dependency for parameter '{$parameter->getName()}'");
            }
        }

        return $dependencies;
    }

    /**
 * 문자열을 조건에 따라 네임스페이스로 변환하는 메서드
 */
    private function resolveNamespace(string $id): string
    {
        // 기본 네임스페이스
        $namespace = '';

        // 조건에 따라 네임스페이스 설정
        if (strpos($id, 'Admin') !== false) {
            if (strpos($id, 'Helper') !== false) {
                $namespace = 'Web\\Admin\\Helper\\';
            } elseif (strpos($id, 'Service') !== false) {
                $namespace = 'Web\\Admin\\Service\\';
            } elseif (strpos($id, 'Model') !== false) {
                $namespace = 'Web\\Admin\\Model\\';
            }
        } else {
            if (strpos($id, 'Service') !== false) {
                $namespace = 'Web\\PublicHtml\\Service\\';
            } elseif (strpos($id, 'Model') !== false) {
                $namespace = 'Web\\PublicHtml\\Model\\';
            } elseif (strpos($id, 'Helper') !== false || strpos($id, 'Manager') !== false) {
                $namespace = 'Web\\PublicHtml\\Helper\\';
            }
        }

        // 네임스페이스와 ID 결합하여 반환
        return $namespace . $id;
    }

    private function __clone() {}
    public function __wakeup() {}
}