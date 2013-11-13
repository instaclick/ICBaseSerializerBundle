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
use IC\Bundle\Base\SerializerBundle\Entity\ProxyHandler;
use PhpOption\None;

/**
 * Doctrine Proxy Subscriber
 *
 * This is designed to work with the proxy handler and the custom array collection handler.
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
        $context        = $event->getContext();
        $flag           = $context->attributes->get(ProxyHandler::ENABLE_HANDLER);
        $skipSubscriber = ($flag instanceof None);
        $object         = $event->getObject();

        if ( ! $skipSubscriber && ($object instanceof Proxy || $object instanceof ORMProxy)) {
            return;
        }

        return parent::onPreSerialize($event);
    }
}
