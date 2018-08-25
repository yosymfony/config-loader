<?php

/*
 * This file is part of the Yosymfony\Config-loader.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\ConfigLoader;

use Yosymfony\ConfigLoader\Exception\LoaderLoadException;

/**
 * Loads configuration sources
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class ConfigLoader
{
    /** @var LoaderResolverInterface */
    private $loaderResolver;

    /**
     * Constructor
     *
     * @param LoaderInterface[] $loaders List of loaders
     */
    public function __construct(array $loaders)
    {
        $this->loaderResolver = new LoaderResolver($loaders);
    }

    /**
     * Loads a resource such as a file or an inline configuration
     *
     * @param string $resource Filename or string representation
     * @param string $type     The resource type. Doesn't set this argument in
     *                         case of a filename passes as resource
     *
     * @return RepositoryInterface
     *
     * @throws LoaderLoadException If the loader not found
     */
    public function load($resource, $type = null) : RepositoryInterface
    {
        $repository = $this->resolveLoader($resource, $type)->load($resource, $type);

        return $repository;
    }

    private function resolveLoader($resource, $type = null) : LoaderInterface
    {
        $loader = $this->loaderResolver->resolveLoader($resource, $type);

        if ($loader === null) {
            throw new LoaderLoadException($resource, $type);
        }

        return $loader;
    }
}
