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

use Comely\IO\DependencyInjection\Services\Service;

/**
 * Class Services
 * @package Comely\IO\DependencyInjection
 */
class Services
{
    /** @var DependencyInjectionContainer */
    private $diContainer;
    /** @var array */
    private $services;

    /**
     * Services constructor.
     * @param DependencyInjectionContainer $diContainer
     */
    public function __construct(DependencyInjectionContainer $diContainer)
    {
        $this->diContainer = $diContainer;
        $this->services = [];
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->services);
    }

    /**
     * @param string $key
     * @return Service
     */
    public function service(string $key): Service
    {
        if ($this->has($key)) {
            return $this->services[$key];
        }

        $newService = new Service($key);
        $this->services[$key] = $newService;
        return $newService;
    }
}