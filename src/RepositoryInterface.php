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

/**
 * Interface for configuration repositories
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
interface RepositoryInterface extends \ArrayAccess, \Countable, \Iterator
{
    /**
     * Returns the value associated with the key or default value
     *
     * @param string $key     Key name
     * @param mixed  $default Default value
     *
     * @return mixed The value in the $key or default
     */
    public function get(string $key, $default);

    /**
     * Sets the value to a key
     *
     * @param string $key   The key name
     * @param mixed  $value The value
     *
     * @return void
     */
    public function set(string $key, $value) : void;

    /**
     * Deletes a key
     *
     * @param string $key Key name
     *
     * @return void
     */
    public function del(string $key) : void;

    /**
     * Performs the union between the current repository and the repository
     * passed as argument. The repository passed as argument has less precedence.
     *
     * @param RepositoryInterface $repository
     *
     * @return RepositoryInterface A new repository
     */
    public function union(RepositoryInterface $repository) : RepositoryInterface;

    /**
     * Performs the intersection between the current repository and the repository
     * passed as argument. The repository passed as argument has less precedence.
     *
     * @param RepositoryInterface $repository
     *
     * @return RepositoryInterface A new repository
     */
    public function intersection(RepositoryInterface $repository) : RepositoryInterface;

    /**
     * Returns an array representation of the repository
     *
     * @return array
     */
    public function getArray() : array;
}
