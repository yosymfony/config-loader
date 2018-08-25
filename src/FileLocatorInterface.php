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

/**
 * This file is based on Symfony FileLocatorInterface (Config component)
 * created by Fabien Potencier <fabien@symfony.com>.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
interface FileLocatorInterface
{
    /**
     * Returns a full path for a given file name.
     *
     * @param string      $name        The file name to locate
     * @param string|null $currentPath The current path
     *
     * @return array Array of full file paths
     *
     * @throws \InvalidArgumentException        If $name is empty
     * @throws FileNotFoundException If a file is not found
     */
    public function locate(string $name, string $currentPath = null) : array;

    /**
     * Returns a full path for a given file name.
     * Only take care the first occurrence of the file in case there are several files
     * in different locations with the same name.
     *
     * @param string      $name        The file name to locate
     * @param string|null $currentPath The current path
     *
     * @return string Full file path
     *
     * @throws \InvalidArgumentException        If $name is empty
     * @throws FileNotFoundException If a file is not found
     */
    public function locateFirst(string $name, string $currentPath = null) : string;
}
