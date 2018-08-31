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

use Yosymfony\ConfigLoader\Exception;

/**
 * Abstract class used by built-in loaders.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
abstract class ConfigFileLoader implements LoaderInterface
{
    private const DIST_EXTENSION = 'dist';
    private const IMPORT_KEY = 'imports';

    /** @var LoaderResolverInterface */
    private $loaderResolver;
    /** @var FileLocatorInterface */
    private $locator;
    /** @var string */
    private $currentDir;

    public function __construct(FileLocatorInterface $locator)
    {
        $this->locator = $locator;
    }

    /**
     * Resolve the location of a file following the hierarchy:
     *    1. filename.ext
     *    2. filename.ext.dist (if filename.ext does not exist).
     *
     *    or
     *
     *    filename.ext.dist if the .dist is included in the resource.
     *
     * @param string $resource Filename path
     *
     * @return string The filename location
     *
     * @throws \InvalidArgumentException When the file is not found
     */
    public function getLocation(string $resource) : string
    {
        if (!$this->isDistExtension($resource)) {
            try {
                return $this->getLocator()->locateFirst($resource, $this->currentDir);
            } catch (\InvalidArgumentException $ex) {
                $resource = $resource.'.'.self::DIST_EXTENSION;
            }
        }

        return  $this->getLocator()->locateFirst($resource, null);
    }

    /**
     * Sets the current directory.
     *
     * @param string $dir
     */
    public function setCurrentDir($dir)
    {
        $this->currentDir = $dir;
    }

    /**
     * Returns the file locator used by this loader.
     *
     * @return FileLocatorInterface
     */
    public function getLocator() : FileLocatorInterface
    {
        return $this->locator;
    }

    public function setLoaderResolver(LoaderResolverInterface $loaderResolver) : void
    {
        $this->loaderResolver = $loaderResolver;
    }

    /**
     * Returns the Loader Resolver implementation
     *
     * @return LoaderResolverInterface
     */
    protected function getLoaderResolver() : LoaderResolverInterface
    {
        return $this->loaderResolver;
    }

    /**
     * Has the file resource a ".dist" extension?
     *
     * @param string $resource The filename
     *
     * @return bool
     */
    protected function isDistExtension(string $resource) : bool
    {
        return pathinfo($resource, PATHINFO_EXTENSION) === self::DIST_EXTENSION;
    }

    /**
     * Parses the repositories in "imports" key
     *
     * @param Repository $repository
     * @param string     $file
     *
     * @return RepositoryInterface
     *
     * @throws InvalidArgumentException If error with "imports" key
     */
    protected function parseImports(RepositoryInterface $repository, string $file) : RepositoryInterface
    {
        if (!isset($repository[self::IMPORT_KEY])) {
            return $repository;
        }

        if (!is_array($repository[self::IMPORT_KEY])) {
            $keyName = self::IMPORT_KEY;
            $message = "The \"{$keyName}\" key should contain an array in {$file}. Check your YAML syntax.";
            throw new \InvalidArgumentException($message);
        }

        foreach ($repository[self::IMPORT_KEY] as $import) {
            if (!is_array($import)) {
                $import = ['resource' => $import];
            }
            
            $ignoreErrors = isset($import['ignore_errors']) ? (bool) $import['ignore_errors'] : false;

            $importedResource = $this->import($import['resource'], null, $ignoreErrors, $file);

            if ($importedResource) {
                $repository = $repository->union($importedResource);
            }
        }

        $repository->del(self::IMPORT_KEY);

        return $repository;
    }

    /**
     * Checks if the file has the extension passed as argument. This method
     * is aware about "dist" files
     *
     * @param string $file
     * @param string $extension Extension to check without dot. e.g: "json"
     *
     * @return bool
     */
    protected function hasResourceExtension(string $file, string $extension) : bool
    {
        return preg_match("#\.{$extension}(\.dist)?$#", $file) === 1;
    }

    /**
     * Reads a file
     *
     * @param string $file The name of the file
     *
     * @return string The file's content
     *
     * @throws BadFileException If the file is not a file or it is not readable
     */
    protected function readFile(string $file) : string
    {
        if (is_file($file) === false) {
            throw new BadFileException("The file \"{$file}\" is not a file.", $file);
        }

        if (is_readable($file) === false) {
            throw new BadFileException("Unable to open \"{$file}\" as the file is not readable.", $file);
        }

        return file_get_contents($file);
    }

    private function import(string $resource, string $type = null, bool $ignoreErrors = false, string $sourceResource = null) : ?RepositoryInterface
    {
        $loader = $this->getLoaderResolver()->resolveLoader($resource, $type);

        if ($loader === null) {
            if (!$ignoreErrors) {
                throw new LoaderLoadException($resource, $type);
            }

            return null;
        }

        return $loader->load($resource, $type);
    }
}
