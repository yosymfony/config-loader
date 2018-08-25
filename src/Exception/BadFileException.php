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
 * Exception for when there is a problem reading a file: the file is not a file
 * or there is not "read" permission
 */
class BadFileException extends \InvalidArgumentException
{
    private $resourcefile;

    public function __construct(string $message = '', string $resourcefile = null, \Exception $previous = null)
    {
        parent::__construct($message, $code = 0, $previous);
        $this->resourcefile = $resourcefile;
    }

    public function getResourcefile() : string
    {
        return $this->resourcefile;
    }
}
