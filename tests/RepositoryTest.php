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
use Yosymfony\ConfigLoader\Repository;

class RepositoryTest extends TestCase
{
    private $repository;

    public function setUp()
    {
        $this->repository = new Repository();
    }

    public function testConstructMustCreateAEmptyRepository() : void
    {
        $repository = new Repository();

        $this->assertCount(0, $repository);
    }

    public function testConstructMustCreateARepositoryWithElements() : void
    {
        $key = 'name';
        $value = 'Víctor';
        $repository = new Repository([$key => $value]);

        $this->assertCount(1, $repository);
        $this->assertEquals($value, $repository[$key]);
    }

    public function testSetMustSetTheValueOfAKey() : void
    {
        $key = 'name';
        $value = 'Víctor';
        $this->repository->set($key, $value);

        $this->assertEquals($value, $this->repository[$key]);
    }

    public function testOffsetGetMustReturnTheValueAssociatedWithTheKey() : void
    {
        $key = 'name';
        $value = 'Víctor';
        $this->repository->set($key, $value);

        $this->assertEquals($value, $this->repository[$key]);
    }

    public function testGetMustReturnTheValueAssociatedWithTheKey() : void
    {
        $key = 'name';
        $value = 'Víctor';
        $this->repository->set($key, $value);

        $this->assertEquals($value, $this->repository->get($key));
    }

    public function testGetMustReturnDefaultValueWhenKeyNotFound() : void
    {
        $defaultValue = '';
        $this->assertEquals($defaultValue, $this->repository->get('fool', $defaultValue));
    }

    public function testGetMustReturnNullWhenKeyNotFoundAndADefaultValueWasNotSet() : void
    {
        $this->assertNull($this->repository->get('fool'));
    }

    public function testGetArrayMustReturnAnArrayWithTheRepositoryValues() : void
    {
        $key = 'name';
        $value = 'Víctor';

        $this->repository[$key] = $value;

        $this->assertEquals([
            $key => $value,
        ], $this->repository->getArray());
    }

    public function testOffsetUnsetMustUnsetTheKey() : void
    {
        $key = 'name';
        $this->repository[$key] = 'Víctor';

        unset($this->repository[$key]);

        $this->assertCount(0, $this->repository);
    }

    public function testDelMustDeleteTheKey() : void
    {
        $this->repository['val'] = 'value';

        $this->repository->del('val');

        $this->assertCount(0, $this->repository);
    }

    public function testUnionMustReturnTheUnionOfTwoRepositories()
    {
        $repositoryA = new Repository();
        $repositoryA['port'] = 25;
        $repositoryA['server'] = 'localhost';

        $repositoryB = new Repository();
        $repositoryB['port'] = 24;
        $repositoryB['server'] = 'yourname.com';
        $repositoryB['secure'] = true;

        $union = $repositoryA->union($repositoryB);

        $this->assertEquals(new Repository([
            'port' => 25,
            'server' => 'localhost',
            'secure' => true,
        ]), $union);
    }

    public function testIntersectionMustReturnTheIntersectionOfTwoRepositories()
    {
        $repositoryA = new Repository();
        $repositoryA['port'] = 25;
        $repositoryA['server'] = 'localhost';

        $repositoryB = new Repository();
        $repositoryB['port'] = 24;
        $repositoryB['server'] = 'yourname.com';
        $repositoryB['secure'] = true;

        $intersection = $repositoryA->intersection($repositoryB);

        $this->assertEquals(new Repository([
            'port' => 25,
            'server' => 'localhost',
        ]), $intersection);
    }
}
