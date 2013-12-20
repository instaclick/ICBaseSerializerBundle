<?php
/**
 * @copyright 2013 Instaclick Inc.
 */

namespace IC\Bundle\Base\SerializerBundle\Tests\EventListener\Serializer;

use IC\Bundle\Base\SerializerBundle\Entity\ProxyHandler;
use IC\Bundle\Base\SerializerBundle\EventListener\Serializer\DoctrineProxySubscriber;
use IC\Bundle\Base\TestBundle\Test\TestCase;
use JMS\Serializer\SerializationContext;
use PhpOption\None;
use PhpOption\Some;

/**
 * Doctrine proxy subscriber Test
 *
 * @group ICBaseSerializerBundle
 * @group Unit
 * @group EventListener
 *
 * @author David Maignan <davidm@nationalfibre.net>
 */
class DoctrineProxySubscriberTest extends TestCase
{
    /**
     * Test onPreSerialize for a Proxy
     */
    public function testOnPreSerializeProxy()
    {
        $object  = $this->createMock('Doctrine\Common\Persistence\Proxy');
        $context = $this->createContext(None::create());
        $event   = $this->createEventMock($context, $object, 1);

        $event->expects($this->never())
            ->method('getType');

        $subscriber = new DoctrineProxySubscriber();
        $subscriber->onPreSerialize($event);
    }

    /**
     * Test onPreSerialize for ORM Proxy
     */
    public function testOnPreSerializeORMProxy()
    {
        $object  = $this->createMock('Doctrine\ORM\Proxy\Proxy');
        $context = $this->createContext(None::create());
        $event   = $this->createEventMock($context, $object, 1);

        $event->expects($this->never())
            ->method('getType');

        $subscriber = new DoctrineProxySubscriber();
        $subscriber->onPreSerialize($event);
    }

    /**
     * Test onPreSerialize for entity
     */
    public function testOnPreSerialize()
    {
        $object  = $this->createMock('IC\Bundle\Base\SerializerBundle\Tests\MockObject\Entity');
        $context = $this->createContext(Some::create('mock value'));
        $event   = $this->createEventMock($context, $object, 2);

        $event->expects($this->once())
            ->method('getType');

        $subscriber = new DoctrineProxySubscriber();
        $subscriber->onPreSerialize($event);
    }

    /**
     * Create mock event
     *
     * @param \JMS\Serializer\SerializationContext $context             Context for the serialization
     * @param mixed                                $object              Entity serialized
     * @param integer                              $numberOfExpectation number of expectation
     *
     * @return mixed
     */
    private function createEventMock($context, $object, $numberOfExpectation)
    {
        $event = $this->createMock('JMS\Serializer\EventDispatcher\PreSerializeEvent');

        $event->expects($this->once())
            ->method('getContext')
            ->will($this->returnValue($context));

        $event->expects($this->exactly($numberOfExpectation))
            ->method('getObject')
            ->will($this->returnValue($object));

        return $event;
    }

    /**
     * @param mixed $flag
     *
     * @return \JMS\Serializer\SerializationContext
     */
    private function createContext($flag)
    {
        $context = new SerializationContext();
        $context->attributes->set(ProxyHandler::ENABLE_HANDLER, $flag);

        return $context;
    }
}
