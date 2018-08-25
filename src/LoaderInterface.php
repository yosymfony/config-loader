<?php

/*
 * This file is part of the Yosymfony Config-loader.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Yosymfony\ConfigLoader;

/**
 * LoaderInterface is the interface implemented by all loader classes
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
interface LoaderInterface
{
    /**
     * Loads a resource.
     *
     * @param mixed       $resource The resource
     * @param string|null $type     The resource type or null if unknown
     *
     * @throws \Exception If something went wrong
     */
    public function load(string $resource, string $type = null) : RepositoryInterface;

    /**
     * Returns whether this class supports the given resource.
     *
     * @param mixed       $resource A resource
     * @param string|null $type     The resource type or null if unknown
     *
     * @return bool True if this class supports the given resource, false otherwise
     */
    public function supports(string $resource, string $type = null) : bool;

    /**
     * Sets the loader resolver. Useful in case load other kind of files from a loader.
     * This method is invoked by a LoaderResolver implementation
     *
     * @param LoaderResolverInterface $loaderResolver
     */
    public function setLoaderResolver(LoaderResolverInterface $loaderResolver) : void;
}
