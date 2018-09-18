<?php

namespace Poseso\Settings\Tests\Unit;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Poseso\Settings\Cache\Cache;
use Poseso\Settings\Stores\ArrayStore;
use Illuminate\Cache\Repository as CacheRepo;
use Illuminate\Cache\ArrayStore as CacheRepoStore;

class RepositoryTest extends TestCase
{
    protected function tearDown()
    {
        m::close();
    }
    public function testStoreCanBeSetAndRetrieved()
    {
        $repo = $this->getRepository();
        $store = new ArrayStore();
        $repo->setStore($store);
        $this->assertEquals($store, $repo->getStore());
    }
    public function testCacheCanBeSetAndRetrieved()
    {
        $repo = $this->getRepository();
        $cache = new Cache(new CacheRepo(new CacheRepoStore()));
        $repo->setCache($cache);
        $this->assertEquals($cache, $repo->getCache());
    }
    public function testGetReturnsValue()
    {
        $repo = $this->getRepository();
        $repo->getStore()->shouldReceive('get')->once()->with('foo')->andReturn('bar');
        $this->assertEquals('bar', $repo->get('foo'));
    }
    public function testDefaultValueIsReturned()
    {
        $repo = $this->getRepository();
        $repo->getStore()->shouldReceive('get')->times(2)->andReturn(null);
        $this->assertEquals('bar', $repo->get('foo', 'bar'));
        $this->assertEquals('baz', $repo->get('boom', function () {
            return 'baz';
        }));
    }
    public function testGetReturnsMultipleValuesWhenGivenAnArray()
    {
        $repo = $this->getRepository();
        $repo->getStore()->shouldReceive('getMultiple')->once()->with(['foo', 'bar', 'fuz'])->andReturn([
            'foo' => 'bar',
            'bar' => 'baz',
            'fuz' => null,
        ]);
        $this->assertEquals(['foo' => 'bar', 'bar' => 'baz', 'fuz' => null], $repo->get(['foo', 'bar', 'fuz']));
    }
    public function testGetReturnsMultipleValuesWhenGivenAnArrayWithDefaultValues()
    {
        $repo = $this->getRepository();
        $repo->getStore()->shouldReceive('getMultiple')->once()->with(['foo', 'bar'])->andReturn([
            'foo' => null,
            'bar' => 'baz',
        ]);
        $this->assertEquals(['foo' => 'default', 'bar' => 'baz'], $repo->get(['foo' => 'default', 'bar']));
    }
    public function testItemCanBeSet()
    {
        $repo = $this->getRepository();
        $repo->getStore()->shouldReceive('set')->with($key = 'foo', $value = 'bar');
        $repo->set($key, $value);
    }
    public function testMultipleItemsCanBeSet()
    {
        $repo = $this->getRepository();
        $repo->getStore()->shouldReceive('setMultiple')->once()->with(['foo' => 'bar', 'bar' => 'baz']);
        $repo->set(['foo' => 'bar', 'bar' => 'baz']);
        $repo = $this->getRepository();
        $repo->getStore()->shouldReceive('setMultiple')->once()->with(['foo' => 'bar', 'bar' => 'baz']);
        $repo->setMultiple(['foo' => 'bar', 'bar' => 'baz']);
    }
    public function testHasMethod()
    {
        $repo = $this->getRepository();
        $repo->getStore()->shouldReceive('has')->once()->with('foo')->andReturnFalse();
        $repo->getStore()->shouldReceive('has')->once()->with('bar')->andReturnTrue();
        $this->assertFalse($repo->has('foo'));
        $this->assertTrue($repo->has('bar'));
    }
    public function testForgettingKey()
    {
        $repo = $this->getRepository();
        $repo->getStore()->shouldReceive('forget')->once()->with('a-key')->andReturn(true);
        $repo->forget('a-key');
    }
    public function testForgettingMultipleKey()
    {
        $repo = $this->getRepository();
        $repo->getStore()->shouldReceive('forgetMultiple')->once()->with(['foo', 'bar'])->andReturn(true);
        $repo->forgetMultiple(['foo', 'bar']);
        $repo->getStore()->shouldReceive('forgetMultiple')->once()->with(['baz', 'qux'])->andReturn(true);
        $repo->forget(['baz', 'qux']);
    }
    public function testScope()
    {
        $repo = $this->getRepository();
        $this->assertEquals('', $repo->getScope());
        $repo->getStore()->shouldReceive('scope')->with('foo');
        $this->assertNotEquals(spl_object_id($repo), spl_object_id($repo = $repo->scope('foo')));
        $this->assertEquals('foo', $repo->getScope());
    }
    public function testDefaultValuesCanBeSetAndRetrieved()
    {
        $repo = $this->getRepository();
        $repo->setStore(new ArrayStore());
        $this->assertNull($repo->getDefault('foo'));
        $this->assertEquals([], $repo->getDefault());
        $repo->setDefault('foo', 'bar');
        $repo->setDefault(['bar' => 'baz', 'baz' => 'qux']);
        $this->assertEquals('bar', $repo->getDefault('foo'));
        $this->assertEquals(['foo' => 'bar', 'bar' => 'baz', 'baz' => 'qux'], $repo->getDefault());
        $this->assertEquals('bar', $repo->get('foo'));
        $this->assertNull($repo->get('qux'));
        $this->assertEquals(['foo' => 'bar', 'bar' => 'baz', 'baz' => 'qux', 'qux' => null], $repo->get([
            'foo',
            'bar',
            'baz',
            'qux',
        ]));
        $repo->forgetDefault('foo');
        $this->assertNull($repo->getDefault('foo'));
        $repo->forgetDefault(['bar', 'baz']);
        $this->assertEquals([], $repo->getDefault());
        $repo->setDefault('qux', 'pax');
        $repo->forgetDefault();
        $this->assertEquals([], $repo->getDefault());
    }
    public function testRegisterMacroWithNonStaticCall()
    {
        $repo = $this->getRepository();
        $repo::macro(__CLASS__, function () {
            return 'Taylor';
        });
        $this->assertEquals($repo->{__CLASS__}(), 'Taylor');
    }
    public function testArrayAccess()
    {
        $repo = $this->getRepository();
        $repo->getStore()->shouldReceive('set')->with('foo', 'qux');
        $repo['foo'] = 'qux';
        $repo->getStore()->shouldReceive('has')->andReturnTrue();
        $this->assertTrue(isset($repo['foo']));
        $repo->getStore()->shouldReceive('get')->with('foo')->andReturn('qux');
        $this->assertEquals($repo->get('foo'), $repo['foo']);
        $this->assertEquals($repo->get('foo'), 'qux');
        $repo->getStore()->shouldReceive('forget')->with('foo');
        unset($repo['foo']);
    }
    public function testCloning()
    {
        $repo = $this->getRepository();
        $repo->setCache(m::spy('Poseso\Settings\Cache\Cache'));
        $repo->setStore(new ArrayStore());
        $repo2 = clone $repo;
        $this->assertNotEquals(spl_object_id($repo->getStore()), spl_object_id($repo2->getStore()));
        $this->assertNotEquals(spl_object_id($repo->getCache()), spl_object_id($repo2->getCache()));
    }
    protected function getRepository()
    {
        $dispatcher = new \Illuminate\Events\Dispatcher(m::mock('Illuminate\Container\Container'));
        $store = m::spy('Poseso\Settings\Contracts\StoreContract');
        $store->allows(['getName' => 'a-store']);
        $repository = new \Poseso\Settings\Repository($store);
        $repository->setEventDispatcher($dispatcher);
        return $repository;
    }
}