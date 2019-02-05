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

namespace Comely\IO\DependencyInjection\Services;

use Comely\IO\DependencyInjection\Exception\ServicesException;

/**
 * Class ServiceInjectProps
 * @package Comely\IO\DependencyInjection\Services
 */
class ServiceInjectProps
{
    /** @var array */
    private $props;

    /**
     * ServiceInjectProps constructor.
     */
    public function __construct()
    {
        $this->props = [];
    }

    /**
     * @param string $propName
     * @param $value
     * @return ServiceInjectProps
     */
    public function add(string $propName, $value): self
    {
        if (!preg_match('/^[a-zA-Z0-9\_]{2,}$/', $propName)) {
            throw new ServicesException('Invalid property name');
        }

        $this->props[$propName] = $value;
        return $this;
    }

    /**
     * @return array
     */
    public function getAll(): array
    {
        return $this->props;
    }
}