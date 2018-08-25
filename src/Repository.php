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
 * Simple implementation of a configuration repository.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class Repository implements RepositoryInterface
{
    protected $repository = [];

    /**
     * Construct a configuration repository from an array
     *
     * @param array $values
     */
    public function __construct(array $values = [])
    {
        $this->repository = $values;
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $key, $default = null)
    {
        return isset($this->repository[$key]) ? $this->repository[$key] : $default;
    }

    /**
     * {@inheritdoc}
     */
    public function set(string $key, $value) : void
    {
        $this->offsetSet($key, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function del(string $key) : void
    {
        if (array_key_exists($key, $this->repository)) {
            unset($this->repository[$key]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function union(RepositoryInterface $repository) : RepositoryInterface
    {
        $union = function (array $r1, array $r2) use (&$union) {
            $res = $r1;
            foreach ($r2 as $k => $v) {
                if (isset($res[$k]) && is_array($r1[$k])) {
                    $res[$k] = $union($r1[$k], $v);
                } elseif (!isset($res[$k])) {
                    $res[$k] = $v;
                }
            }

            return $res;
        };

        return new self($union($this->getArray(), $repository->getArray()));
    }

    /**
     * {@inheritdoc}
     */
    public function intersection(RepositoryInterface $repository) : RepositoryInterface
    {
        $interception = function ($main, $second) {
            $result = new Repository();
            $keysMain = array_keys($main->getArray());
            $keysSecond = array_keys($second->getArray());
            $keys = array_intersect($keysMain, $keysSecond);

            foreach ($keys as $key) {
                $result[$key] = $main[$key];
            }

            return $result;
        };

        return $interception($this, $repository);
    }

    /**
     * {@inheritdoc}.
     */
    public function getArray() : array
    {
        return $this->repository;
    }

    /**
     * Set a new key (From ArrayAccess interface).
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->repository[] = $value;
        } else {
            $this->repository[$offset] = $value;
        }
    }

    /**
     * Check if a key exists (from ArrayAccess interface).
     */
    public function offsetExists($offset)
    {
        return isset($this->repository[$offset]);
    }

    /**
     * Delete a key (from ArrayAccess interface).
     */
    public function offsetUnset($offset)
    {
        unset($this->repository[$offset]);
    }

    /**
     * Retrueve a key (from ArrayAccess interface).
     */
    public function offsetGet($offset)
    {
        return isset($this->repository[$offset]) ? $this->repository[$offset] : null;
    }

    /**
     * Count of element of a repository (from Countable interface).
     */
    public function count()
    {
        return count($this->repository);
    }

    /**
     * Set the pointer to the first element (from Iterator interface).
     */
    public function rewind()
    {
        return reset($this->repository);
    }

    /**
     * Get the current element (from Iterator interface).
     */
    public function current()
    {
        return current($this->repository);
    }

    /**
     * Get the current position (from Iterator interface).
     */
    public function key()
    {
        return key($this->repository);
    }

    /**
     * Set the pointer to the next element (from Iterator interface).
     */
    public function next()
    {
        return next($this->repository);
    }

    /**
     * Checks if the current position is valid (from Iterator interface).
     */
    public function valid()
    {
        return key($this->repository) !== null;
    }
}
