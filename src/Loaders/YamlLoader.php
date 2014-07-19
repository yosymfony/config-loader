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

/**
 * YAML file loader
 * 
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class YamlLoader extends ConfigFileLoader
{
    public function load($resource, $type = null)
    {
        if (false == class_exists('Symfony\Component\Yaml\Yaml'))
        {
            throw new \RuntimeException('Symfony\Component\Yaml\Yaml parser is required to read yaml files.');
        }
        
        if(null === $type)
        {
            $resource = $this->getLocation($resource);
        }
        
        $data = Yaml::parse($resource);
        $repository = new Repository();
        $repository->load($data ? $data : array());
        
        return $repository;
    }
    
    public function supports($resource, $type = null)
    {
        return 'yaml' === $type || (is_string($resource) && preg_match('#\.yml(\.dist)?$#', $resource));
    }
}