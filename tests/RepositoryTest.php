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

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Yosymfony\ConfigLoader\Config;
use Yosymfony\ConfigLoader\Repository;
use Yosymfony\ConfigLoader\Loaders\TomlLoader;
use Yosymfony\ConfigLoader\Loaders\YamlLoader;
use Yosymfony\ConfigLoader\Loaders\JsonLoader;
use PHPUnit\Framework\TestCase;

class RepositoryTest extends TestCase
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

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage This repository only accept configuration from arrays
     */
    public function testLoadShouldThrowInvalidArgumentException()
    {
        $repository = new Repository();
        $repository->load(null);
    }

    public function testRewindShouldReturnFalse()
    {
        $repository = new Repository();
        $this->assertFalse($repository->rewind());
    }

    public function testCurrentShouldReturnFalse()
    {
        $repository = new Repository();
        $this->assertFalse($repository->current());
    }

    public function testKeyShouldReturnNull()
    {
        $repository = new Repository();
        $this->assertNull($repository->key());
    }

    public function testNextShouldReturnNull()
    {
        $repository = new Repository();
        $this->assertFalse($repository->next());
    }

    public function testValidShouldReturnFalse()
    {
        $repository = new Repository();
        $this->assertFalse($repository->valid());
    }

    public function testRepositoryAddKey()
    {
        $repository = new Repository();
        $repository['name'] = 'YoSymfony';

        $this->assertEquals($repository->get('name'), 'YoSymfony');
        $this->assertEquals($repository['name'], 'YoSymfony');
    }

    public function testRepositoryAddKeyWithSet()
    {
        $repository = new Repository();
        $repository->set('name', 'YoSymfony');

        $this->assertEquals($repository->get('name'), 'YoSymfony');
        $this->assertEquals($repository['name'], 'YoSymfony');
    }

    public function testRepositoryGetWithDefault()
    {
        $repository = new Repository();

        $this->assertEquals($repository->get('name', 'no-val'), 'no-val');
        $this->assertEquals($repository->get('name', true), true);
        $this->assertEquals($repository->get('name', false), false);
        $this->assertEquals($repository->get('name', 10), 10);
        $this->assertEquals($repository->get('name', 1.0), 1.0);
        $this->assertEquals($repository->get('name', null), null);
    }

    public function testRespositoryGetRaw()
    {
        $repository = new Repository();
        $this->assertTrue(is_array($repository->getRaw()));

        $repository['val'] = 'value';
        $this->assertCount(1, $repository->getRaw());
    }

    public function testRespositoryGetArray()
    {
        $repository = new Repository();
        $this->assertTrue(is_array($repository->getArray()));

        $repository['val'] = 'value';
        $this->assertCount(1, $repository->getArray());
    }

    public function testRepositoryUnsetKey()
    {
        $repository = new Repository();
        $repository['val'] = 'value';
        unset($repository['val']);

        $this->assertCount(0, $repository);
    }

    public function testRepositoryDeleteKey()
    {
        $repository = new Repository();
        $repository['val'] = 'value';
        $repository->del('val');

        $this->assertCount(0, $repository);
    }

    public function testRepositoryNullKey()
    {
        $repository = new Repository();
        $repository[null] = 1;

        $this->assertEquals($repository[0], 1);
    }

    public function testRepositorySetNullKey()
    {
        $repository = new Repository();
        $repository->set(null, 1);

        $this->assertEquals($repository[0], 1);
    }

    public function testRepositoryUnion()
    {
        $repositoryA = new Repository();
        $repositoryA['port'] = 25;
        $repositoryA['server'] = 'localhost';

        $repositoryB = new Repository();
        $repositoryB['port'] = 24;
        $repositoryB['server'] = 'mail.yourname.com';
        $repositoryB['secure'] = true;

        $union = $repositoryA->union($repositoryB);
        $this->assertInstanceOf('Yosymfony\ConfigLoader\RepositoryInterface', $union);
        $this->assertCount(3, $union);
        $this->assertEquals($union['port'], 25);
        $this->assertEquals($union['server'], 'localhost');
        $this->assertEquals($union['secure'], true);

        $this->assertCount(2, $repositoryA);
    }

    public function testRepositoryUnionMainMinor()
    {
        $repositoryA = new Repository();
        $repositoryA['port'] = 25;

        $repositoryB = new Repository();
        $repositoryB['port'] = 24;
        $repositoryB['server'] = 'mail.yourname.com';
        $repositoryB['secure'] = true;

        $union = $repositoryA->union($repositoryB);
        $this->assertInstanceOf('Yosymfony\ConfigLoader\RepositoryInterface', $union);
        $this->assertCount(3, $union);
        $this->assertEquals($union['port'], 25);
        $this->assertEquals($union['server'], 'mail.yourname.com');
        $this->assertEquals($union['secure'], true);
    }

    public function testRepositoryIntersection()
    {
        $repositoryA = new Repository();
        $repositoryA['port'] = 25;
        $repositoryA['server'] = 'localhost';

        $repositoryB = new Repository();
        $repositoryB['port'] = 24;
        $repositoryB['server'] = 'mail.yourname.com';
        $repositoryB['secure'] = true;

        $intersection = $repositoryA->intersection($repositoryB);
        $this->assertInstanceOf('Yosymfony\ConfigLoader\RepositoryInterface', $intersection);
        $this->assertCount(2, $intersection);
        $this->assertEquals($intersection['port'], 25);
        $this->assertEquals($intersection['server'], 'localhost');
        $this->assertArrayNotHasKey('secure', $intersection);

        $this->assertCount(2, $repositoryA);
    }

    public function testRepositoryDefinitions()
    {
        $repository = $this->config->load("port = 25\n server = \"localhost\"", Config::TYPE_TOML);
        $repository->validateWith(new ConfigDefinitions());
        $repoArray = $repository->getArray();

        $this->assertSame(25, $repoArray['port']);
        $this->assertSame('localhost', $repoArray['server']);
    }

    /**
     * @expectedException Symfony\Component\Config\Definition\Exception\InvalidTypeException
     */
    public function testRepositoryFailDefinitions()
    {
        $repository = $this->config->load("port = \"25\"\n server = \"localhost\"", Config::TYPE_TOML);
        $repository->validateWith(new ConfigDefinitions());
    }

    public function testRepositoryUnionIsRecursive()
    {
        $r1 = $this->config->load('config-merge-to.json');
        $r2 = $this->config->load('config-merge-from.json');
        $r3 = $r1->union($r2);
        $expected = array(
            'default' => array(
                'port' => 25,
                'server' => 'mail.example.com',
            ),
        );

        $this->assertEquals($expected, $r3->getArray());
    }
}

/**
 * Configuration Definitions rules example for test purpose.
 */
class ConfigDefinitions implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root(0);

        $rootNode->children()
            ->integerNode('port')
                ->end()
            ->scalarNode('server')
                ->end()
        ->end();

        return $treeBuilder;
    }
}
