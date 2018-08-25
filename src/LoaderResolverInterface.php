<?php

/*
 * This file is part of the Yosymfony config-loader.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\ConfigLoader;

/**
 * Interface for a loader resolver
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
interface LoaderResolverInterface
{
    /**
     * Returns a loader able to load the resource
     *
     * @param string      $resource A resource
     * @param string|null $type The resource type or null if unknown
     *
     * @return LoaderInterface|null The loader or null if loader not found
     *
     */
    public function resolveLoader(string $resource, string $type = null) : ?LoaderInterface;
}
