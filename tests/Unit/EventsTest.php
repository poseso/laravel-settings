<?php

namespace Poseso\Settings\Tests\Unit;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Poseso\Settings\Repository;
use Poseso\Settings\Scopes\Scope;
use Poseso\Settings\Stores\ArrayStore;
use Poseso\Settings\Events\PropertyMissed;
use Poseso\Settings\Events\PropertyRemoved;
use Poseso\Settings\Events\PropertyWritten;
use Poseso\Settings\Events\PropertyReceived;
use Poseso\Settings\Events\AllSettingsRemoved;
use Poseso\Settings\Events\AllSettingsReceived;

class EventsTest extends TestCase
{
    public function tearDown()
    {
        m::close();
    }
    public function testGetTriggersEvents()
    {
        $dispatcher = $this->getDispatcher();
        $repository = $this->getRepository($dispatcher);
        $dispatcher->shouldReceive('dispatch')->once()->with($this->assertEventMatches(PropertyMissed::class, ['key' => 'foo']));
        $this->assertNull($repository->get('foo'));
        $dispatcher->shouldReceive('dispatch')->once()->with($this->assertEventMatches(PropertyReceived::class, [
            'key' => 'baz',
            'value' => 'qux',
        ]));
        $this->assertEquals('qux', $repository->get('baz'));
    }
    public function testAllTriggersEvents()
    {
        $dispatcher = $this->getDispatcher();
        $repository = $this->getRepository($dispatcher);
        $dispatcher->shouldReceive('dispatch')->once()->with($this->assertEventMatches(AllSettingsReceived::class));
        $this->assertEquals(['baz' => 'qux'], $repository->all());
    }
    public function testSetTriggersEvents()
    {
        $dispatcher = $this->getDispatcher();
        $repository = $this->getRepository($dispatcher);
        $dispatcher->shouldReceive('dispatch')->once()->with($this->assertEventMatches(PropertyWritten::class, [
            'key' => 'foo',
            'value' => 'bar',
        ]));
        $repository->set('foo', 'bar');
    }
    public function testForgetTriggersEvents()
    {
        $dispatcher = $this->getDispatcher();
        $repository = $this->getRepository($dispatcher);
        $dispatcher->shouldReceive('dispatch')->once()->with($this->assertEventMatches(PropertyRemoved::class, ['key' => 'baz']));
        $this->assertTrue($repository->forget('baz'));
    }
    public function testFlushTriggersEvents()
    {
        $dispatcher = $this->getDispatcher();
        $repository = $this->getRepository($dispatcher);
        $dispatcher->shouldReceive('dispatch')->once()->with($this->assertEventMatches(AllSettingsRemoved::class));
        $this->assertTrue($repository->flush());
    }
    public function testStoreNameCanBeSetAndRetrieved()
    {
        $event = new PropertyWritten('foo', 'bar');
        $event->setStoreName('foo');
        $this->assertEquals('foo', $event->getStoreName());
    }
    public function testScopeCanBeSetAndRetrieved()
    {
        $event = new PropertyWritten('foo', 'bar');
        $event->setScope($scope = new Scope('foo'));
        $this->assertEquals($scope, $event->getScope());
    }
    protected function assertEventMatches($eventClass, $properties = [])
    {
        return m::on(function ($event) use ($eventClass, $properties) {
            if (! $event instanceof $eventClass) {
                return false;
            }
            foreach ($properties as $name => $value) {
                if ($event->$name != $value) {
                    return false;
                }
            }
            return true;
        });
    }
    protected function getDispatcher()
    {
        return m::mock('Illuminate\Events\Dispatcher');
    }
    protected function getRepository($dispatcher)
    {
        $repository = new Repository(new ArrayStore());
        $repository->set('baz', 'qux');
        $repository->setEventDispatcher($dispatcher);
        return $repository;
    }
}