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

use Comely\IO\DependencyInjection\Exception\RepositoryException;
use Comely\Kernel\Comely;

/**
 * Class Repository
 * @package Comely\IO\DependencyInjection
 */
class Repository implements \Countable
{
    /** @var array */
    private $instances;
    /** @var int */
    private $count;

    /**
     * Repository constructor.
     */
    public function __construct()
    {
        $this->instances = [];
        $this->count = 0;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return $this->count;
    }

    /**
     * @return array
     */
    public function list(): array
    {
        $instances = [];
        foreach ($this->instances as $key => $instance) {
            $instances[$key] = get_class($instance);
        }

        return $instances;
    }

    /**
     * @param $instance
     * @param string|null $key
     * @return int
     * @throws RepositoryException
     */
    public function push($instance, string $key = null): int
    {
        if (!is_object($instance)) {
            throw new RepositoryException('Repository can only hold instances. First argument must be an object');
        }

        $key = $key ?? Comely::baseClassName(get_class($instance));
        $key = $this->processKey(__METHOD__, $key);
        $this->instances[$key] = $instance;
        $this->count++;

        return $this->count;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        $key = $this->processKey(__METHOD__, $key);
        return array_key_exists($key, $this->instances);
    }

    /**
     * @param string $key
     * @return mixed
     * @throws RepositoryException
     */
    public function pull(string $key)
    {
        $key = $this->processKey(__METHOD__, $key);
        if (!$this->has($key)) {
            throw new RepositoryException(sprintf('No instance found for key "%s"', $key));
        }

        return $this->instances[$key];
    }

    /**
     * @param string $method
     * @param string $in
     * @return string
     * @throws Exception\DependencyInjectionException
     */
    private function processKey(string $method, string $in): string
    {
        return DependencyInjectionContainer::ProcessKey($method, $in);
    }
}