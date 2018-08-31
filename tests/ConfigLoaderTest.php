<?php

/*
 * This file is part of the Yosymfony\Config-loader.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\ConfigLoader\Tests;

use PHPUnit\Framework\TestCase;
use Yosymfony\ConfigLoader\LoaderInterface;
use Yosymfony\ConfigLoader\Repository;
use Yosymfony\ConfigLoader\FileLocator;
use Yosymfony\ConfigLoader\ConfigLoader;
use Yosymfony\ConfigLoader\Loaders\TomlLoader;
use Yosymfony\ConfigLoader\Loaders\YamlLoader;
use Yosymfony\ConfigLoader\Loaders\JsonLoader;

class ConfigLoaderTest extends TestCase
{
    public function testLoadMustCallMethodLoadOfALoader() : void
    {
        $expectedRepository = new Repository(['name' => 'test']);
        $loaderStub = $this->createMock(LoaderInterface::class);
        $loaderStub->method('supports')
            ->willReturn(true);
        $loaderStub->method('load')
            ->willReturn($expectedRepository);

        $configLoader = new ConfigLoader([$loaderStub]);
        $currentRepository = $configLoader->load("myconfig.json");

        $this->assertEquals($expectedRepository, $currentRepository);
    }

    /**
     * @expectedException Yosymfony\ConfigLoader\Exception\LoaderLoadException
     * @expectedExceptionMessage Loader not found for the resource: "config-file.fake".
     */
    public function testLoadMustFailWhenLoaderNotFound() : void
    {
        $loaderStub = $this->createMock(LoaderInterface::class);
        $config = new ConfigLoader([$loaderStub]);
        
        $config->load('config-file.fake');
    }
}
