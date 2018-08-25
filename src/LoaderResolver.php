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

use Yosymfony\ConfigLoader\Exception\LoaderLoadException;

/**
 * Loader resolver implementation
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class LoaderResolver implements LoaderResolverInterface
{
    private $loaders = [];

    /**
     * Constructor
     *
     * @param LoaderInterface[] The list of loaders
     *
     * @throws InvalidArgumentException If the Array of loaders is empty or null
     */
    public function __construct(array $loaders)
    {
        $this->loadLoaders($loaders);
    }
    
    /**
     * {@inheritdoc}
     */
    public function resolveLoader(string $resource, string $type = null) : ?LoaderInterface
    {
        foreach ($this->loaders as $loader) {
            if ($loader->supports($resource, $type)) {
                return $loader;
            }
        }

        return null;
    }

    private function loadLoaders(array $loaders) : void
    {
        if ($loaders === null || count($loaders) === 0) {
            throw new \InvalidArgumentException('The Array of loaders is empty or null.');
        }

        foreach ($loaders as $loader) {
            $this->loadLoader($loader);
        }
    }

    private function loadLoader(LoaderInterface $loader) : void
    {
        $loader->setLoaderResolver($this);
        $this->loaders[] = $loader;
    }
}
