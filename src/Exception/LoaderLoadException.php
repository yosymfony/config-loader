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
 * Exception loading a loader
 */
class LoaderLoadException extends \RuntimeException
{
    private $resource;
    private $type;

    public function __construct(string $resource, string $type = null)
    {
        $this->resource = $resource;
        $this->type = $type;
        parent::__construct($this->composeMessage($resource, $type));
    }

    public function getResource() : string
    {
        return $this->resource;
    }

    public function getType() : string
    {
        return $this->type;
    }

    private function composeMessage(string $resource, string $type = null) : string
    {
        $message = "Loader not found for the resource: \"{$resource}\"";

        if (empty($type) == false) {
            $message .= " and type: \"{$type}\"";
        }

        return $message.'.';
    }
}
