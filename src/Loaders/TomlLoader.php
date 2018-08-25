<?php

/*
 * This file is part of the Yosymfony\Config-loader.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\ConfigLoader\Loaders;

use Yosymfony\Toml\Toml;
use Yosymfony\ConfigLoader\ConfigFileLoader;
use Yosymfony\ConfigLoader\Repository;
use Yosymfony\ConfigLoader\RepositoryInterface;

/**
 * TOML file loader
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class TomlLoader extends ConfigFileLoader
{
    public const TYPE = "toml";

    /**
     * {@inheritdoc}
     *
     * @throws RuntimeException If Yosymfony Toml package is not installed
     */
    public function load(string $resource, string $type = null) : RepositoryInterface
    {
        if (class_exists('Yosymfony\Toml\Toml') === false) {
            throw new \RuntimeException('"Yosymfony Toml" is required to read TOML files.');
        }

        $resourceContent = $resource;
        
        if (empty($type)) {
            $file = $this->getLocation($resource);
            $resourceContent = $this->readFile($file);
        }

        $parsedResource = Toml::parse($resourceContent);
        $repository = new Repository($parsedResource ?? []);

        return $this->parseImports($repository, $resource);
    }

    /**
     * {@inheritdoc}
     */
    public function supports(string $resource, string $type = null) : bool
    {
        return $type === self::TYPE || $this->hasResourceExtension($resource, 'toml');
    }
}
