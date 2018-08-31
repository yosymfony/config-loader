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
use Yosymfony\ConfigLoader\Loaders\JsonLoader;
use Yosymfony\ConfigLoader\LoaderResolver;
use Yosymfony\ConfigLoader\Repository;

class JsonLoaderTest extends TestCase
{
    private $loader;

    public function setUp() : void
    {
        $locator = new FileLocator(array(__dir__.'/../fixtures'));
        $this->loader = new JsonLoader($locator);
    }

    public function testLoadMustParseInlineString() : void
    {
        $expectedRepository = new Repository(['name' => 'acme']);
        $json = '{"name":"acme"}';
        
        $repository = $this->loader->load($json, JsonLoader::TYPE);

        $this->assertEquals($expectedRepository, $repository);
    }

    public function testLoadMustParseAFile() : void
    {
        $expectedRepository = new Repository(['port' => 443]);
        $jsonFile = 'example.json';
        
        $repository = $this->loader->load($jsonFile);

        $this->assertEquals($expectedRepository, $repository);
    }

    public function testLoadMustParseResourcesInIncludeSection() : void
    {
        $this->loader->setLoaderResolver(new LoaderResolver([$this->loader]));
        $expectedRepository = new Repository([
            'port' => 443,
            'server' => 'myexample.com',
        ]);
        $jsonFile = 'example-with-includes.json';
        
        $repository = $this->loader->load($jsonFile);

        $this->assertEquals($expectedRepository, $repository);
    }

    /**
     * @expectedException Yosymfony\ConfigLoader\Exception\FileNotFoundException
     * @expectedExceptionMessage The file "fakeFile.json.dist" does not exist
     */
    public function testLoadMustFailWhenTheFileDoesNotExists() : void
    {
        $fakeFile = 'fakeFile.json';
        $this->loader->load($fakeFile);
    }
}
