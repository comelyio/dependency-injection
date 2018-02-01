<?php
/**
 * This file is part of Comely package.
 * https://github.com/comelyio/comely
 *
 * Copyright (c) 2016-2018 Furqan A. Siddiqui <hello@furqansiddiqui.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code or visit following link:
 * https://github.com/comelyio/comely/blob/master/LICENSE
 */

declare(strict_types=1);

namespace Comely\IO\DependencyInjection\Services;

use Comely\IO\DependencyInjection\Exception\ServicesException;

/**
 * Class Service
 * @package Comely\IO\DependencyInjection\Services
 */
class Service
{
    /** @var string */
    private $key;
    /** @var null|string */
    private $class;
    /** @var array */
    private $methods;
    /** @var null|ServiceInjectMethod */
    private $constructor;
    /** @var null|ServiceInjectProps */
    private $props;

    /**
     * Service constructor.
     * @param string $key
     * @throws ServicesException
     */
    public function __construct(string $key)
    {
        $this->key = $key;
        $this->methods = [];
    }

    /**
     * @param string $className
     * @return Service
     * @throws ServicesException
     */
    public function class(string $className): self
    {
        if (!preg_match('/^[a-zA-Z0-9\_\\\]{4,}$/', $className)) {
            throw new ServicesException(sprintf('Invalid class name signature for key "%s"', $this->key));
        }

        return $this;
    }

    /**
     * @return ServiceInjectMethod
     */
    public function constructor(): ServiceInjectMethod
    {
        if (!$this->constructor) {
            $this->constructor = new ServiceInjectMethod("__construct");
        }

        return $this->constructor;
    }

    /**
     * @param string $name
     * @return ServiceInjectMethod
     */
    public function method(string $name): ServiceInjectMethod
    {
        $injectMethod = new ServiceInjectMethod($name);
        $this->methods[$name] = $injectMethod;
        return $injectMethod;
    }

    /**
     * @return ServiceInjectProps
     */
    public function props(): ServiceInjectProps
    {
        if (!$this->props) {
            $this->props = new ServiceInjectProps();
        }

        return $this->props;
    }

    /**
     * @param array ...$args
     * @return mixed
     * @throws ServicesException
     */
    public function createInstance(...$args)
    {
        // Check if class exists
        if (!$this->class || !class_exists($this->class)) {
            throw new ServicesException(
                sprintf('Failed to create instance for "%s", class "%s" does not exist', $this->key, $this->class ?? "")
            );
        }

        // Constructor
        $className = $this->class;
        $constructorArgs = $args;
        if ($this->constructor) {
            $constructorArgs = array_merge($constructorArgs, $this->constructor->getArgs());
        }

        $constructorArgs = $this->processArgs($constructorArgs);

        // Create instance
        $object = new $className(...$constructorArgs);

        // Methods
        /** @var $method ServiceInjectMethod */
        foreach ($this->methods as $method) {
            // No need to check if method exists but quite possibly magic __call method may be implemented
            call_user_func_array(
                [$object, $method->getName()], // Method name
                $this->processArgs($method->getArgs()) // Method arguments processed
            );
        }

        // Properties
        if ($this->props) {
            $props = $this->props->getAll();
            foreach ($props as $propName => $propValue) {
                $object->$propName = $this->processArg($propValue);
            }
        }

        // Return created object
        return $object;
    }

    /**
     * @param array ...$args
     * @return array
     * @throws ServicesException
     */
    private function processArgs(...$args): array
    {
        $processed = [];
        foreach ($args as $arg) {
            $arg = $this->processArg($arg);
            $processed[] = $arg;
        }

        return $processed;
    }

    /**
     * @param $arg
     * @return mixed
     * @throws ServicesException
     */
    private function processArg($arg)
    {
        if (is_object($arg) && $arg instanceof Service) {
            $arg = $arg->createInstance();
        }

        return $arg;
    }
}