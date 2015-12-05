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

/**
 * TOML file loader.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class TomlLoader extends ConfigFileLoader
{
    public function load($resource, $type = null)
    {
        if (false == class_exists('Yosymfony\Toml\Toml')) {
            throw new \RuntimeException('Yosymfony\Toml parser is required to read toml files.');
        }

        if (null === $type) {
            $resource = $this->getLocation($resource);
        }

        $data = Toml::parse($resource);
        $repository = new Repository();
        $repository->load($data ? $data : array());

        return $this->parseImports($repository, $resource);
    }

    public function supports($resource, $type = null)
    {
        return 'toml' === $type || (is_string($resource) && preg_match('#\.toml(\.dist)?$#', $resource));
    }
}
