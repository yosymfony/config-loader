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

use Symfony\Component\Yaml\Yaml;
use Yosymfony\ConfigLoader\ConfigFileLoader;
use Yosymfony\ConfigLoader\Repository;
use Yosymfony\ConfigLoader\RepositoryInterface;

/**
 * YAML file loader
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class YamlLoader extends ConfigFileLoader
{
    public const TYPE = "yaml";

    /**
     * {@inheritdoc}
     *
     * @throws RuntimeException If Symfony Yaml package is not installed
     */
    public function load(string $resource, string $type = null) : RepositoryInterface
    {
        if (false == class_exists('Symfony\Component\Yaml\Yaml')) {
            throw new \RuntimeException('"Symfony Yaml" is required to read YAML files.');
        }

        $resourceContent = $resource;

        if (empty($type)) {
            $file = $this->getLocation($resource);
            $resourceContent = $this->readFile($file);
        }

        $parsedResource = Yaml::parse($resourceContent);
        $repository = new Repository($parsedResource ?? []);

        return $this->parseImports($repository, $resource);
    }

    /**
     * {@inheritdoc}
     */
    public function supports(string $resource, string $type = null) : bool
    {
        return $type === self::TYPE || $this->hasResourceExtension($resource, 'yml');
    }
}
