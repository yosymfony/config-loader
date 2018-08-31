<?php

/*
 * This file is part of the Yosymfony\Config-loader.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\ConfigLoader\Tests\Loaders;

use PHPUnit\Framework\TestCase;
use Yosymfony\ConfigLoader\FileLocator;
use Yosymfony\ConfigLoader\Loaders\TomlLoader;
use Yosymfony\ConfigLoader\LoaderResolver;
use Yosymfony\ConfigLoader\Repository;

class TomlLoaderTest extends TestCase
{
    private $loader;

    public function setUp() : void
    {
        $locator = new FileLocator(array(__dir__.'/../Fixtures'));
        $this->loader = new TomlLoader($locator);
    }

    public function testLoadMustParseInlineString() : void
    {
        $expectedRepository = new Repository(['name' => 'acme']);
        $toml = 'name = "acme"';
        
        $repository = $this->loader->load($toml, TomlLoader::TYPE);

        $this->assertEquals($expectedRepository, $repository);
    }

    public function testLoadMustParseAFile() : void
    {
        $expectedRepository = new Repository(['port' => 443]);
        $tomlFile = 'example.toml';
        
        $repository = $this->loader->load($tomlFile);

        $this->assertEquals($expectedRepository, $repository);
    }

    public function testLoadMustParseResourcesInIncludeSection() : void
    {
        $this->loader->setLoaderResolver(new LoaderResolver([$this->loader]));
        $expectedRepository = new Repository([
            'port' => 443,
            'server' => 'myexample.com',
        ]);
        $jsonFile = 'example-with-includes.toml';
        
        $repository = $this->loader->load($jsonFile);

        $this->assertEquals($expectedRepository, $repository);
    }

    /**
     * @expectedException Yosymfony\ConfigLoader\Exception\FileNotFoundException
     * @expectedExceptionMessage The file "fakeFile.toml.dist" does not exist in: /home/vagrant/Code/config-loader/tests/Loaders/../Fixtures.
     */
    public function testLoadMustFailWhenTheFileDoesNotExists() : void
    {
        $fakeFile = 'fakeFile.toml';
        $this->loader->load($fakeFile);
    }
}
