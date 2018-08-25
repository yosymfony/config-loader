<?php

/*
 * This file is part of the Yosymfony config-loader.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\ConfigLoader\Exception;

/**
 * File not found exception.
 */
class FileNotFoundException extends \InvalidArgumentException
{
    private $paths;

    public function __construct(string $message = '', int $code = 0, \Exception $previous = null, array $paths = [])
    {
        parent::__construct($message, $code, $previous);
        $this->paths = $paths;
    }

    public function getPaths() : array
    {
        return $this->paths;
    }
}
