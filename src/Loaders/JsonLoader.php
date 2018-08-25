<?php

/*
 * This file is part of the Yosymfony config-loader.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\ConfigLoader\Loaders;

use Yosymfony\ConfigLoader\ConfigFileLoader;
use Yosymfony\ConfigLoader\Repository;
use Yosymfony\ConfigLoader\RepositoryInterface;

/**
 * JSON file loader
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class JsonLoader extends ConfigFileLoader
{
    public const TYPE = "json";

    /**
     * {@inheritdoc}
     *
     * @throws RuntimeException If JSON parse error
     */
    public function load(string $resource, string $type = null) : RepositoryInterface
    {
        $resourceContent = $resource;

        if (empty($type)) {
            $file = $this->getLocation($resource);
            $resourceContent = $this->readFile($file);
        }

        $parsedResource = $this->parseJson($resourceContent);
        $errorMsg = $this->getLastErrorMessage(json_last_error());

        if ($errorMsg) {
            $msg = $type ? sprintf('JSON parse error: %s.', $errorMsg) : sprintf('JSON parse error: %s at %s', $errorMsg, $resource);

            throw new \RuntimeException($msg);
        }

        $repository = new Repository($parsedResource ?? []);

        return $this->parseImports($repository, $resource);
    }

    /**
     * {@inheritdoc}
     */
    public function supports(string $resource, string $type = null) : bool
    {
        return $type === self::TYPE || $this->hasResourceExtension($resource, 'json');
    }

    /**
     * @return mixed
     */
    private function parseJson(string $resource)
    {
        return json_decode($resource, true);
    }

    private function getLastErrorMessage(int $errorCode) : ?string
    {
        $errors = [
            JSON_ERROR_NONE => null,
            JSON_ERROR_DEPTH => 'Maximum stack depth exceeded',
            JSON_ERROR_STATE_MISMATCH => 'Underflow or the modes mismatch',
            JSON_ERROR_CTRL_CHAR => 'Unexpected control character found',
            JSON_ERROR_SYNTAX => 'Syntax error, malformed JSON',
            JSON_ERROR_UTF8 => 'Malformed UTF-8 characters, possibly incorrectly encoded',
        ];

        if (array_key_exists($errorCode, $errors)) {
            return $errors[$errorCode];
        }

        return sprintf('Unknown error code: "%s"', $errorCode);
    }
}
