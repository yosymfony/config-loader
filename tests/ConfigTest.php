<?php

/*
 * This file is part of the Yosymfony\Config-loader.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\ConfigLoader\tests;

use Symfony\Component\Config\FileLocator;
use Yosymfony\ConfigLoader\Config;
use Yosymfony\ConfigLoader\Repository;
use Yosymfony\ConfigLoader\Loaders\TomlLoader;
use Yosymfony\ConfigLoader\Loaders\YamlLoader;
use Yosymfony\ConfigLoader\Loaders\JsonLoader;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    protected $config;

    public function setUp()
    {
        $locator = new FileLocator(array(__dir__.'/Fixtures'));

        $this->config = new Config(array(
            new TomlLoader($locator),
            new YamlLoader($locator),
            new JsonLoader($locator),
        ));
    }

    public function testJsonInline()
    {
        $repository = $this->config->load('{ "var": "my value" }', Config::TYPE_JSON);
        $this->assertNotNull($repository);
        $this->assertEquals($repository->get('var'), 'my value');
        $this->assertEquals($repository['var'], 'my value');
        $this->assertTrue(is_array($repository->getArray()));
    }

    public function testTomlInline()
    {
        $repository = $this->config->load('var = "my value"', Config::TYPE_TOML);
        $this->assertNotNull($repository);
        $this->assertEquals($repository->get('var'), 'my value');
        $this->assertEquals($repository['var'], 'my value');
        $this->assertEquals($repository->get('key_not_exist', 'default'), 'default');
        $this->assertTrue(is_array($repository->getArray()));
    }

    public function testYamlInline()
    {
        $repository = $this->config->load('var: "my value"', Config::TYPE_YAML);
        $this->assertNotNull($repository);
        $this->assertEquals($repository->get('var'), 'my value');
        $this->assertEquals($repository['var'], 'my value');
        $this->assertEquals($repository->get('key_not_exist', 'default'), 'default');
        $this->assertTrue(is_array($repository->getArray()));
    }

    public function testJsonFile()
    {
        $repository = $this->config->load('config.json');
        $this->assertNotNull($repository);
        $this->assertEquals($repository->get('port'), 25);
        $this->assertEquals($repository['port'], 25);
        $this->assertEquals($repository->get('server'), 'mail.yourname.com');
        $this->assertEquals($repository['server'], 'mail.yourname.com');
    }

    public function testTomlFile()
    {
        $repository = $this->config->load('config.toml');
        $this->assertNotNull($repository);
        $this->assertEquals($repository->get('port'), 25);
        $this->assertEquals($repository['port'], 25);
        $this->assertEquals($repository->get('server'), 'mail.yourname.com');
        $this->assertEquals($repository['server'], 'mail.yourname.com');
    }

    public function testTomlDistFile()
    {
        $repository = $this->config->load('config.toml.dist');
        $this->assertNotNull($repository);
        $this->assertEquals($repository->get('port'), 25);
        $this->assertEquals($repository['port'], 25);
        $this->assertEquals($repository->get('server'), 'mail2.yourname.com');
        $this->assertEquals($repository['server'], 'mail2.yourname.com');
    }

    public function testYamlFile()
    {
        $repository = $this->config->load('config.yml');
        $this->assertNotNull($repository);
        $this->assertEquals($repository->get('port'), 25);
        $this->assertEquals($repository['port'], 25);
        $this->assertEquals($repository->get('server'), 'mail.yourname.com');
        $this->assertEquals($repository['server'], 'mail.yourname.com');
    }

    public function testYamlDistFile()
    {
        $repository = $this->config->load('config.yml.dist');
        $this->assertNotNull($repository);
        $this->assertEquals($repository->get('port'), 25);
        $this->assertEquals($repository['port'], 25);
        $this->assertEquals($repository->get('server'), 'mail2.yourname.com');
        $this->assertEquals($repository['server'], 'mail2.yourname.com');
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
        $repository = $this->config->load('{ "var": "value"', Config::TYPE_TOML);
    }

    /**
     * @expectedException Yosymfony\Toml\Exception\ParseException
     */
    public function testTomlInlineFail()
    {
        $repository = $this->config->load('var = "my value', Config::TYPE_TOML);
    }

    /**
     * @expectedException Symfony\Component\Yaml\Exception\ParseException
     */
    public function testYamlInlineFail()
    {
        $repository = $this->config->load('var : [ elemnt', Config::TYPE_YAML);
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
}
