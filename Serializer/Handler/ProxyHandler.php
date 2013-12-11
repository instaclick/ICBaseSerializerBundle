<?php
namespace IC\Bundle\Base\SerializerBundle\Serializer\Handler;

use Doctrine\ORM\Proxy\Proxy;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\Context;
use JMS\Serializer\Exception\SkipStepException;
use IC\Bundle\Base\SecurityBundle\Routing\Router;
use IC\Bundle\Base\ComponentBundle\Entity\Entity;
use IC\Bundle\Base\SerializerBundle\Entity\ProxyHandler as ProxyHandlerFlag;
use PhpOption\None;

/**
 * This handler prevent to load the relationship and provide the REST Api entrypoint for it
 *
 * @author Juti Noppornpitak <jutin@nationalfibre.net>
 * @author Kinn Coelho Julião <kinnj@nationalfibre.net>
 */
class ProxyHandler implements SubscribingHandlerInterface
{
    /**
     * @var \IC\Bundle\Base\SecurityBundle\Routing\Router
     */
    private $router;

    /**
     * Inject the Router
     *
     * @param \IC\Bundle\Base\SecurityBundle\Routing\Router $router
     */
    public function setRouter(Router $router)
    {
        $this->router = $router;
    }

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
                'type'      => 'IC\Bundle\Base\ComponentBundle\Entity\Entity',
                'method'    => 'serializeProxy',
            ),
        );
    }

    /**
     * Serialize
     *
     * @param \JMS\Serializer\JsonSerializationVisitor $visitor
     * @param mixed                                    $entity
     * @param array                                    $type
     * @param \JMS\Serializer\Context                  $context
     *
     * @throws \JMS\Serializer\Exception\SkipStepException
     *
     * @return array
     */
    public function serializeProxy(JsonSerializationVisitor $visitor, $entity, array $type, Context $context)
    {
        $flag = $context->attributes->get(ProxyHandlerFlag::ENABLE_HANDLER);

        if ($flag instanceof None) {
            throw new SkipStepException('Skip no flag');
        }

        if ($visitor->getRoot() === null) {
            throw new SkipStepException('Skip the root');
        }

        if ( ! ($entity instanceof Entity)) {
            throw new SkipStepException('Skip the non-entity object of class ' . get_class($entity));
        }

        if ( ! $entity instanceof Proxy && $entity instanceof Entity) {
            throw new SkipStepException('Skip the entity');
        }

        if (($entity instanceof Proxy && $entity->__isInitialized())) {
            throw new SkipStepException('Skip the proxy');
        }

        $typePartList  = explode('\\', $type['name']);
        $parameterList = array(
            'packageName'    => strtolower($typePartList[2]),
            'subPackageName' => strtolower(substr($typePartList[3], 0, strpos($typePartList[3], 'Bundle'))),
            'entityName'     => strtolower($typePartList[5]),
            'id'             => $entity->getId(),
        );

        $route = $this->router->generate('ICBaseRestBundle_Rest_Get', $parameterList, Router::ABSOLUTE_URL);

        return array(
            'id'   => $entity->getId(),
            '_url' => $route,
        );
    }
}
