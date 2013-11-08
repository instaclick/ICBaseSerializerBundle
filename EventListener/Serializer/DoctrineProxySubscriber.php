<?php
/**
 * @copyright 2013 Instaclick Inc.
 */

namespace IC\Bundle\Base\SerializerBundle\EventListener\Serializer;

use Doctrine\ORM\PersistentCollection;
use Doctrine\Common\Persistence\Proxy;
use Doctrine\ORM\Proxy\Proxy as ORMProxy;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;
use JMS\Serializer\EventDispatcher\Subscriber\DoctrineProxySubscriber as DoctrineProxySubscriberBase;

/**
 * DoctrineProxy Serialize Subscriber.
 *
 * @author Kinn Coelho Julião <kinnj@nationalfibre.net>
 * @author Enzo Rizzo <enzor@nationalfibre.net>
 * @author Fabio Batista Silva <fabios@nationalfibre.net>
 */

class DoctrineProxySubscriber extends DoctrineProxySubscriberBase
{
    /**
     * {@inheritdoc}
     */
    public function onPreSerialize(PreSerializeEvent $event)
    {
        $object = $event->getObject();

        if ( ! $object instanceof Proxy && ! $object instanceof ORMProxy) {
            return;
        }

        return parent::onPreSerialize($event);
    }
}
