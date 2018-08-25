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
use Yosymfony\ConfigLoader\FileLocator;
use Yosymfony\ConfigLoader\ConfigLoader;
use Yosymfony\ConfigLoader\Loaders\TomlLoader;
use Yosymfony\ConfigLoader\Loaders\YamlLoader;
use Yosymfony\ConfigLoader\Loaders\JsonLoader;

class ConfigLoaderTest extends TestCase
{
    protected $config;

    public function setUp()
    {
        $locator = new FileLocator(array(__dir__.'/Fixtures'));

        $this->config = new ConfigLoader([
            new TomlLoader($locator),
            new YamlLoader($locator),
            new JsonLoader($locator),
        ]);
    }

    public function provideFileFormats()
    {
        return array(
            array('json'),
            array('toml'),
            array('yml'),
        );
    }

    public function testJsonInline()
    {
        $repository = $this->config->load('{ "var": "my value" }', JsonLoader::TYPE);
        $this->assertNotNull($repository);
        $this->assertEquals($repository->get('var'), 'my value');
        $this->assertEquals($repository['var'], 'my value');
        $this->assertTrue(is_array($repository->getArray()));
    }

    public function testTomlInline()
    {
        $repository = $this->config->load('var = "my value"', TomlLoader::TYPE);
        $this->assertNotNull($repository);
        $this->assertEquals($repository->get('var'), 'my value');
        $this->assertEquals($repository['var'], 'my value');
        $this->assertEquals($repository->get('key_not_exist', 'default'), 'default');
        $this->assertTrue(is_array($repository->getArray()));
    }

    public function testYamlInline()
    {
        $repository = $this->config->load('var: "my value"', YamlLoader::TYPE);
        $this->assertNotNull($repository);
        $this->assertEquals($repository->get('var'), 'my value');
        $this->assertEquals($repository['var'], 'my value');
        $this->assertEquals($repository->get('key_not_exist', 'default'), 'default');
        $this->assertTrue(is_array($repository->getArray()));
    }

    public function testOnlyDistFile()
    {
        $repository = $this->config->load('config-server.yml');
        $this->assertNotNull($repository);
        $this->assertEquals($repository->get('port'), 80);
        $this->assertEquals($repository['port'], 80);
        $this->assertEquals($repository->get('host'), 'yourname.com');
        $this->assertEquals($repository['host'], 'yourname.com');
    }

    public function testLoadFileWithAbsolutePath()
    {
        $repository = $this->config->load(__dir__.'/Fixtures/config.yml');
        $this->assertNotNull($repository);
        $this->assertEquals($repository->get('port'), 25);
        $this->assertEquals($repository['port'], 25);
        $this->assertEquals($repository->get('server'), 'mail.yourname.com');
        $this->assertEquals($repository['server'], 'mail.yourname.com');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testJsonInlineFail()
    {
        $repository = $this->config->load('{ "var": "value"', JsonLoader::TYPE);
    }

    /**
     * @expectedException Yosymfony\Toml\Exception\ParseException
     */
    public function testTomlInlineFail()
    {
        $repository = $this->config->load('var = "my value', TomlLoader::TYPE);
    }

    /**
     * @expectedException Symfony\Component\Yaml\Exception\ParseException
     */
    public function testYamlInlineFail()
    {
        $repository = $this->config->load('var : [ elemnt', YamlLoader::TYPE);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testJsonFileFail()
    {
        $repository = $this->config->load('configFail.json');
    }

    /**
     * @expectedException Yosymfony\Toml\Exception\ParseException
     */
    public function testTomlFileFail()
    {
        $repository = $this->config->load('configFail.toml');
    }

    /**
     * @expectedException Symfony\Component\Yaml\Exception\ParseException
     */
    public function testYamlFileFail()
    {
        $repository = $this->config->load('configFail.yml');
    }

    /**
     * @dataProvider provideFileFormats
     */
    public function testFormattedFile($format)
    {
        $repository = $this->config->load("config.{$format}");
        $this->assertNotNull($repository);
        $this->assertEquals($repository->get('port'), 25);
        $this->assertEquals($repository['port'], 25);
        $this->assertEquals($repository->get('server'), 'mail.yourname.com');
        $this->assertEquals($repository['server'], 'mail.yourname.com');
    }

    /**
     * @dataProvider provideFileFormats
     */
    public function testFormattedDistFile($format)
    {
        $repository = $this->config->load("config.{$format}.dist");
        $this->assertNotNull($repository);
        $this->assertEquals($repository->get('port'), 25);
        $this->assertEquals($repository['port'], 25);
        $this->assertEquals($repository->get('server'), 'mail2.yourname.com');
        $this->assertEquals($repository['server'], 'mail2.yourname.com');
    }

    /**
     * @dataProvider provideFileFormats
     */
    public function testImportsForFormat($format)
    {
        $repository = $this->config->load("config-imports.{$format}");
        $this->assertArrayHasKey('port', $repository);
    }

    public function testImportsFromDifferentFormats()
    {
        $repository = $this->config->load('config-imports-all.yml');
        $this->assertArrayHasKey('json', $repository);
        $this->assertArrayHasKey('toml', $repository);
        $this->assertArrayHasKey('yaml', $repository);
    }

    /**
     * @expectedException Yosymfony\ConfigLoader\Exception\LoaderLoadException
     * @expectedExceptionMessage Loader not found for the resource: "config-file.fake".
     */
    public function testLoadMustFailWhenLoaderNotFound() : void
    {
        $this->config->load('config-file.fake');
    }
}
