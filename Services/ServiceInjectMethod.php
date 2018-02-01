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
 * Class ServiceInjectMethod
 * @package Comely\IO\DependencyInjection\Services
 */
class ServiceInjectMethod
{
    /** @var string */
    private $name;
    /** @var array */
    private $args;

    /**
     * ServiceInjectMethod constructor.
     * @param string $name
     */
    public function __construct(string $name)
    {
        if(!preg_match('/^[a-zA-Z0-9\_]{2,}$/', $name)) {
            throw new ServicesException('Invalid DI method name');
        }

        $this->name = $name;
        $this->args = [];
    }

    /**
     * @param array ...$args
     * @return ServiceInjectMethod
     */
    public function args(...$args): self
    {
        $this->args = $args;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getArgs(): array
    {
        return $this->args;
    }
}