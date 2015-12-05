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

use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Config\Loader\DelegatingLoader;

/**
 * Load configurations and create repositories.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class Config
{
    const TYPE_TOML = 'toml';
    const TYPE_YAML = 'yaml';
    const TYPE_JSON = 'json';

    private $loaders;
    private $loaderResolver;
    private $delegatingLoader;

    /**
     * Constructor.
     *
     * @param array $loaders
     */
    public function __construct(array $loaders)
    {
        if (null === $loaders || 0 == count($loaders)) {
            throw new \InvalidArgumentException('The Array of loaders is empty');
        }

        $this->loaders = $loaders;
        $this->loaderResolver = new LoaderResolver($this->loaders);
        $this->delegatingLoader = new DelegatingLoader($this->loaderResolver);
    }

    /**
     * Loads a resource like file or inline configuration.
     *
     * @param string $resource A resource
     * @param string $type     The resource type. Don't set this argument in files case.
     *
     * @return RepositoryInterface
     *
     * @throws Symfony\Component\Config\Exception\FileLoaderLoadException If the loader not found.
     * @throws \UnexpectedValueException                                  If the loader not return a repository instance
     */
    public function load($resource, $type = null)
    {
        $repository = $this->delegatingLoader->load($resource, $type);

        if (!$repository instanceof RepositoryInterface) {
            throw new \UnexpectedValueException('The loader must return a repository instance');
        }

        return $repository;
    }
}
