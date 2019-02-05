<?php
/**
 * This file is part of Comely package.
 * https://github.com/comelyio/comely
 *
 * Copyright (c) 2016-2019 Furqan A. Siddiqui <hello@furqansiddiqui.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code or visit following link:
 * https://github.com/comelyio/comely/blob/master/LICENSE
 */

declare(strict_types=1);

namespace Comely\IO\DependencyInjection;

use Comely\IO\DependencyInjection\Exception\DependencyInjectionException;
use Comely\IO\DependencyInjection\Services\Service;
use Comely\Kernel\Extend\ComponentInterface;

/**
 * Class DependencyInjectionContainer
 * @package Comely\IO\DependencyInjection
 */
class DependencyInjectionContainer implements ComponentInterface
{
    /** @var Services */
    private $services;
    /** @var Repository */
    private $repository;

    /**
     * DependencyInjectionContainer constructor.
     */
    public function __construct()
    {
        $this->services = new Services($this);
        $this->repository = new Repository();
    }

    /**
     * @param string $key
     * @return Service
     * @throws DependencyInjectionException
     */
    public function service(string $key): Service
    {
        $key = self::ProcessKey(__METHOD__, $key);
        return $this->services->service($key);
    }

    /**
     * @return Repository
     */
    public function repo(): Repository
    {
        return $this->repository;
    }

    /**
     * Store an instance or add new server
     *
     * If second argument is an instance, it will be stored as-is.
     *
     * If second argument is a class name (string), it will be added as service
     * and instance to Service will be returned
     *
     * @param string $key
     * @param $object
     * @return Service|bool
     * @throws DependencyInjectionException
     * @throws Exception\RepositoryException
     * @throws Exception\ServicesException
     */
    public function add(string $key, $object)
    {
        $key = self::ProcessKey(__METHOD__, $key);
        $objectType = gettype($object);
        switch ($objectType) {
            case "object":
                $this->repository->push($object, $key);
                return true;
            case "string":
                return $this->service($key)->class($object);
            default:
                throw new DependencyInjectionException(
                    sprintf('Cannot store "%s" in Dependency Injection container', $objectType)
                );
        }
    }

    /**
     * @param string $key
     * @param array ...$args
     * @return mixed
     * @throws DependencyInjectionException
     * @throws Exception\RepositoryException
     * @throws Exception\ServicesException
     */
    public function get(string $key, ...$args)
    {
        $key = self::ProcessKey(__METHOD__, $key);
        // Check in repository
        if ($this->repository->has($key)) {
            return $this->repository->pull($key);
        } else {
            return $this->service($key)->createInstance(...$args);
        }
    }

    /**
     * @param string $method
     * @param string $in
     * @return string
     * @throws DependencyInjectionException
     */
    public static function ProcessKey(string $method, string $in): string
    {
        if (!preg_match('/^[a-zA-Z0-9\.\-\_\+\\\]{2,64}$/', $in)) {
            throw DependencyInjectionException::InvalidKey($method);
        }

        return strtolower($in); // Case-insensitivity
    }
}