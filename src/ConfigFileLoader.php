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

use Symfony\Component\Config\Loader\FileLoader;

/**
 * Abstract class used by built-in loaders.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
abstract class ConfigFileLoader extends FileLoader
{
    /**
     * Get the location of a file resource follow the next hierachy:
     *    1. filename.ext
     *    2. filename.ext.dist (if filename.ext not exists).
     *
     *    or
     *
     *    filename.ext.dist if the .dist is included in the resource.
     *
     * @param string $resource Filename path
     *
     * @return string
     *
     * @throws \InvalidArgumentException When the file is not found
     */
    public function getLocation($resource)
    {
        if (false === $this->isDistExtension($resource)) {
            try {
                return $this->getLocator()->locate($resource, null, true);
            } catch (\InvalidArgumentException $ex) {
                $resource = $resource.'.dist';
            }
        }

        return  $this->getLocator()->locate($resource, null, true);
    }

    /**
     * The file resource have .dist extension?
     *
     * @param string $resource
     *
     * @return bool
     */
    public function isDistExtension($resource)
    {
        return 'dist' === pathinfo($resource, PATHINFO_EXTENSION);
    }

    /**
     * Parses the repositories "imports" similar to the Symfony Dependency Injector's YamlFileLoader.
     *
     * @param Repository $repository
     * @param string     $file
     *
     * @return array|void
     *
     * @throws \Exception
     * @throws \Symfony\Component\Config\Exception\FileLoaderImportCircularReferenceException
     * @throws \Symfony\Component\Config\Exception\FileLoaderLoadException
     */
    protected function parseImports(Repository $repository, $file)
    {
        if (!isset($repository['imports'])) {
            return $repository;
        }

        if (!is_array($repository['imports'])) {
            throw new \InvalidArgumentException(sprintf('The "imports" key should contain an array in %s. Check your YAML syntax.', $file));
        }

        foreach ($repository['imports'] as $import) {
            if (!is_array($import)) {
                $import = array('resource' => $import);
            }
            $ignoreErrors = isset($import['ignore_errors']) ? (bool) $import['ignore_errors'] : false;
            $repository = $repository->union($this->import($import['resource'], null, $ignoreErrors, $file));
        }

        return $repository;
    }
}
