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

use Yosymfony\ConfigLoader\Exception\FileNotFoundException;

/**
 * FileLocator uses an array of pre-defined paths to find files
 *
 * This file is based on Symfony FileLocatorInterface (Config component)
 * Created by Fabien Potencier <fabien@symfony.com>.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class FileLocator implements FileLocatorInterface
{
    /** @var array */
    protected $paths;

    /**
     * @param string[] $paths An array of paths where to look for resources
     */
    public function __construct(array $paths = [])
    {
        $this->paths = $paths;
    }

    /**
     * {@inheritdoc}
     */
    public function locate(string $name, string $currentPath = null) : array
    {
        return $this->locateFile($name, $currentPath, false);
    }

    /**
     * {@inheritdoc}
     */
    public function locateFirst(string $name, string $currentPath = null) : string
    {
        return $this->locateFile($name, $currentPath, true);
    }

    /**
     * @return string|array
     */
    protected function locateFile(string $name, string $currentPath = null, bool $first)
    {
        if ($name == '') {
            throw new \InvalidArgumentException('An empty file name is not valid to be located.');
        }

        if ($this->isAbsolutePath($name)) {
            if (!file_exists($name)) {
                $this->throwFileNotFoundException($name);
            }

            return $name;
        }

        $paths = $this->paths;

        if ($currentPath !== null) {
            array_unshift($paths, $currentPath);
        }

        $paths = array_unique($paths);
        $filepaths = $notfound = [];

        foreach ($paths as $path) {
            if (@file_exists($file = $path.DIRECTORY_SEPARATOR.$name)) {
                if ($first) {
                    return $file;
                }
                $filepaths[] = $file;
            } else {
                $notfound[] = $file;
            }
        }

        if (!$filepaths) {
            $this->throwFileNotFoundException($name, $paths, $notfound);
        }

        return $filepaths;
    }

    protected function isAbsolutePath(string $file) : bool
    {
        if ('/' === $file[0] || '\\' === $file[0]
            || (
                strlen($file) > 3 && ctype_alpha($file[0])
                && ':' === $file[1]
                && ('\\' === $file[2] || '/' === $file[2])
            )
            || null !== parse_url($file, PHP_URL_SCHEME)
        ) {
            return true;
        }

        return false;
    }

    private function throwFileNotFoundException(string $file, array $paths = [], array $notFoundPaths = []) : void
    {
        $message = "The file \"{$file}\" does not exist";

        if (\count($paths) === 0) {
            $notFoundPaths[] = $file;
        } else {
            $pathsJoined = implode(', ', $paths);
            $message .= " in: {$pathsJoined}";
        }
        
        $message .= '.';

        throw new FileNotFoundException($message, 0, null, $notFoundPaths);
    }
}
