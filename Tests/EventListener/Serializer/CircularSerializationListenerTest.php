<?php
/**
 * @copyright 2013 Instaclick Inc.
 */

namespace IC\Bundle\Base\SerializerBundle\Tests\EventListener\Serializer;

use IC\Bundle\Base\SerializerBundle\EventListener\Serializer\CircularSerializationListener;
use IC\Bundle\Base\SerializerBundle\Tests\MockObject;
use IC\Bundle\Base\TestBundle\Test\TestCase;
use JMS\Serializer\EventDispatcher\CircularSerializationEvent;

/**
 * Circular Serialization Listener Test
 *
 * @group Unit
 * @group EventListener
 *
 * @author Anthon Pang <anthonp@nationalfibre.net>
 */
class CircularSerializationListenerTest extends TestCase
{
    /**
     * Test getSubscribedEvents
     */
    public function testGetSubscribedEvents()
    {
        $listener     = new CircularSerializationListener;
        $subscription = $listener->getSubscribedEvents();

        $this->assertEquals(1, count($subscription));
        $this->assertEquals('serializer.circular_serialization', $subscription[0]['event']);
        $this->assertEquals('onCircularSerialization', $subscription[0]['method']);
    }

    /**
     * Test onCircularSerialization
     *
     * @param \JMS\Serializer\EventDispatcher\CircularSerializationEvent $event
     *
     * @dataProvider provideOnCircularSerializationNull
     */
    public function testOnCircularSerializationNull($event)
    {
        $listener = new CircularSerializationListener;
        $listener->onCircularSerialization($event);

        $replacement = $event->getReplacement();

        $this->assertTrue($replacement === null);
    }

    /**
     * Data provider for onCircularSerializationNull test
     *
     * @return array
     */
    public function provideOnCircularSerializationNull()
    {
        $data = array();

        // expect no replacement
        $data[] = array($this->prepareEvent(null, array()));

        // expect no replacement because Type does not implement getId()
        $object = new MockObject\Type;
        $object->setType('dummy');

        $data[] = array($this->prepareEvent($object, array()));

        return $data;
    }

    /**
     * Test onCircularSerialization
     *
     * @param \JMS\Serializer\EventDispatcher\CircularSerializationEvent $event
     * @param mixed                                                      $expectedId
     *
     * @dataProvider provideOnCircularSerialization
     */
    public function testOnCircularSerialization($event, $expectedId)
    {
        $listener = new CircularSerializationListener;
        $listener->onCircularSerialization($event);

        $replacement = $event->getReplacement();

        $this->assertEquals(1, count($replacement));
        $this->assertEquals(100, $replacement['id']);
    }

    /**
     * Data provider for onCircularSerialization test
     *
     * @return array
     */
    public function provideOnCircularSerialization()
    {
        $data = array();

        // expect replacement because Entity implements getId()
        $object = new MockObject\Entity;
        $object->setId(100);

        $data[] = array($this->prepareEvent($object, array()), 100);

        return $data;
    }

    /**
     * Prepare event
     *
     * @param mixed $object Object to be serialized
     * @param array $type   Type
     *
     * @return \JMS\Serializer\EventDispatcher\CircularSerializationEvent
     */
    private function prepareEvent($object, array $type)
    {
        $event = new CircularSerializationEvent(
            $this->createMock('JMS\Serializer\Context'),
            $object,
            $type
        );

        return $event;
    }
}
