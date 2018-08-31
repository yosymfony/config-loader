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
use Yosymfony\ConfigLoader\Loaders\YamlLoader;
use Yosymfony\ConfigLoader\LoaderResolver;
use Yosymfony\ConfigLoader\Repository;

class YamlLoaderTest extends TestCase
{
    private $loader;

    public function setUp() : void
    {
        $locator = new FileLocator(array(__dir__.'/../fixtures'));
        $this->loader = new YamlLoader($locator);
    }

    public function testLoadMustParseInlineString() : void
    {
        $expectedRepository = new Repository(['name' => 'acme']);
        $yaml = 'name: "acme"';
        
        $repository = $this->loader->load($yaml, YamlLoader::TYPE);

        $this->assertEquals($expectedRepository, $repository);
    }

    public function testLoadMustParseAFile() : void
    {
        $expectedRepository = new Repository(['port' => 443]);
        $yamlFile = 'example.yml';
        
        $repository = $this->loader->load($yamlFile);

        $this->assertEquals($expectedRepository, $repository);
    }

    public function testLoadMustParseResourcesInIncludeSection() : void
    {
        $this->loader->setLoaderResolver(new LoaderResolver([$this->loader]));
        $expectedRepository = new Repository([
            'port' => 443,
            'server' => 'myexample.com',
        ]);
        $yamlFile = 'example-with-includes.yml';
        
        $repository = $this->loader->load($yamlFile);

        $this->assertEquals($expectedRepository, $repository);
    }

    /**
     * @expectedException Yosymfony\ConfigLoader\Exception\FileNotFoundException
     * @expectedExceptionMessage The file "fakeFile.yml.dist" does not exist
     */
    public function testLoadMustFailWhenTheFileDoesNotExists() : void
    {
        $fakeFile = 'fakeFile.yml';
        $this->loader->load($fakeFile);
    }
}
