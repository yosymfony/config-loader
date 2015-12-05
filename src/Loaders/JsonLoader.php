<?php

/*
 * This file is part of the Yosymfony\Config-loader.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\ConfigLoader\Loaders;

use Yosymfony\ConfigLoader\ConfigFileLoader;
use Yosymfony\ConfigLoader\Repository;

/**
 * JSON file loader.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class JsonLoader extends ConfigFileLoader
{
    /**
     * {@inheritdoc}
     */
    public function load($resource, $type = null)
    {
        if (null === $type) {
            $resource = $this->getLocation($resource);
            $data = $this->loadFile($resource);
        } else {
            $data = $resource;
        }

        $parsed = $this->parseResource($data);
        $errorMsg = $this->getLastErrorMessage(json_last_error());

        if ($errorMsg) {
            $msg = $type ? sprintf('JSON parse error: %s', $errorMsg) : sprintf('JSON parse error: %s at %s', $errorMsg, $resource);

            throw new \RuntimeException($msg);
        }

        $repository = new Repository();
        $repository->load($parsed ? $parsed : array());

        return $this->parseImports($repository, $resource);
    }

    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        return 'json' === $type || (is_string($resource) && preg_match('#\.json(\.dist)?$#', $resource));
    }

    private function parseResource($resource)
    {
        return json_decode($resource, true);
    }

    private function loadFile($resource)
    {
        return file_get_contents($resource);
    }

    /**
     * @param int $errorCode
     */
    private function getLastErrorMessage($errorCode)
    {
        $errors = array(
            JSON_ERROR_NONE => null,
            JSON_ERROR_DEPTH => 'Maximum stack depth exceeded',
            JSON_ERROR_STATE_MISMATCH => 'Underflow or the modes mismatch',
            JSON_ERROR_CTRL_CHAR => 'Unexpected control character found',
            JSON_ERROR_SYNTAX => 'Syntax error, malformed JSON',
            JSON_ERROR_UTF8 => 'Malformed UTF-8 characters, possibly incorrectly encoded',
        );

        return array_key_exists($errorCode, $errors) ? $errors[$errorCode] : sprintf('Unknown error code: %s', $errorCode);
    }
}
