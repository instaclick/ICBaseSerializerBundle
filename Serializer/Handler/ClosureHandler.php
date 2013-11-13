<?php
namespace IC\Bundle\Base\SerializerBundle\Serializer\Handler;

use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\VisitorInterface;
use JMS\Serializer\Handler\SubscribingHandlerInterface;

/**
 * This handler prevent to load a collectio and provide the REST Api Filter entrypoint for it
 *
 * @author Kinn Coelho Julião <kinnj@nationalfibre.net>
 * @author Fabio Batista Silva <fabios@nationalfibre.net>
 * @author Juti Noppornpitak <jutin@nationalfibre.net>
 */
class ClosureHandler implements SubscribingHandlerInterface
{
    /**
     * Get the subscriging methods
     *
     * @return array;
     */
    public static function getSubscribingMethods()
    {
        return array(
            array(
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'format'    => 'json',
                'type'      => 'Closure',
                'method'    => 'skipClosure',
            ),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function skipClosure(VisitorInterface $visitor, \Closure $closure, array $type, Context $context)
    {
        return null;
    }
}
