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

use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Interface that must be implemented by configuration repositories.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
interface RepositoryInterface extends RepositoryOperationInterface, \ArrayAccess, \Countable, \Iterator
{
    /**
     * Loads data of the repository.
     *
     * @param mixed $data
     *
     * @throws \InvalidArgumentException When argument is not valid
     */
    public function load($data);

    /**
     * Returns the value associated with the key.
     *
     * @param string $key     Key name
     * @param mixed  $default Default value
     *
     * @return mixed The value in the $key or default
     */
    public function get($key, $default);

    /**
     * Sets the value to a key.
     *
     * @param string $key   The key name
     * @param mixed  $value The value
     */
    public function set($key, $value);

    /**
     * Deletes a key.
     *
     * @param string $key Key name
     */
    public function del($key);

    /**
     * Validates the configurations values.
     *
     * @param ConfigurationInterface $definition The rules
     *
     * @throws \Exception If any value is not of the expected type, is mandatory and yet undefined, or could not be validated in some other way
     */
    public function validateWith(ConfigurationInterface $definition);

    /**
     * Returns a raw representation of the repository.
     *
     * @return mixed
     */
    public function getRaw();

    /**
     * Returns an array representation of the repository.
     *
     * @return array
     */
    public function getArray();
}
