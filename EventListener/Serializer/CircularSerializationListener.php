<?php
/**
 * @copyright 2013 Instaclick Inc.
 */

namespace IC\Bundle\Base\SerializerBundle\EventListener\Serializer;

use JMS\Serializer\EventDispatcher\CircularSerializationEvent;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;

/**
 * Serializer Visited Listener
 *
 * @author Danilo Cabello <danilo.cabello@gmail.com>
 */
class CircularSerializationListener implements EventSubscriberInterface
{
    /**
     * If an object that is being visited has getId method, switch the object
     * by an array with the id of the object.
     *
     * @param \JMS\Serializer\EventDispatcher\CircularSerializationEvent $event CircularSerializationEvent
     */
    public function onCircularSerialization(CircularSerializationEvent $event)
    {
        $object = $event->getObject();

        if ( ! method_exists($object, 'getId')) {
            return;
        }

        $event->setReplacement(array('id' => $object->getId()));
    }

    /**
     * Returns the event name that this subscriber is listening to and the
     * method name to call when the event happens.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            array('event' => 'serializer.circular_serialization', 'method' => 'onCircularSerialization'),
        );
    }
}
