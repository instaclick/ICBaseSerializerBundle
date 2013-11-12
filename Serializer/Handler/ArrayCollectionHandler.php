<?php
namespace IC\Bundle\Base\SerializerBundle\Serializer\Handler;

use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\VisitorInterface;
use Doctrine\Common\Collections\Collection;
use JMS\Serializer\Handler\ArrayCollectionHandler as BaseArrayCollectionHandler;
use IC\Bundle\Core\SecurityBundle\Routing\Router;
use PhpOption\None;
use JMS\Serializer\Exception\SkipStepException;

/**
 * This handler prevent to load a collectio and provide the REST Api Filter entrypoint for it
 *
 * @author Kinn Coelho Julião <kinnj@nationalfibre.net>
 */
class ArrayCollectionHandler extends BaseArrayCollectionHandler
{
    /**
     * @var \IC\Bundle\Core\SecurityBundle\Routing\Router
     */
    private $router;

    /**
     * Inject the Router
     *
     * @param \IC\Bundle\Core\SecurityBundle\Routing\Router $router
     */
    public function setRouter(Router $router)
    {
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribingMethods()
    {
        $methods = array();
        $formats = array('json');
        $collectionTypes = array(
            'ArrayCollection',
            'Doctrine\Common\Collections\ArrayCollection',
        );

        foreach ($collectionTypes as $type) {
            foreach ($formats as $format) {
                $methods[] = array(
                    'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                    'type' => $type,
                    'format' => $format,
                    'method' => 'serializeCollection',
                );

                $methods[] = array(
                    'direction' => GraphNavigator::DIRECTION_DESERIALIZATION,
                    'type' => $type,
                    'format' => $format,
                    'method' => 'deserializeCollection',
                );
            }
        }

        return $methods;
    }

    /**
     * {@inheritdoc}
     */
    public function serializeCollection(VisitorInterface $visitor, Collection $collection, array $type, Context $context)
    {
        if ($collection->isInitialized()) {
            return $visitor->visitArray($collection, $type, $context);
        }

        $class            = $collection->getTypeClass()->name;
        $type['name']     = $class;
        $parent           = $collection->getOwner();
        $parentClassParts = explode('\\', get_class($parent));
        $parentClassName  = $parentClassParts[(count($parentClassParts) - 1)];
        $parentId         = $parent->getId();
        $typePartList     = explode('\\', $class);
        $parameterList    = array(
            'packageName'    => strtolower($typePartList[2]),
            'subPackageName' => strtolower(substr($typePartList[3], 0, strpos($typePartList[3], 'Bundle'))),
            'entityName'     => strtolower($typePartList[5]),
        );

        $route = $this->router->generate('ICBaseRestBundle_Rest_Filter', $parameterList, Router::ABSOLUTE_URL);
        $route = "{$route}?{$parentClassName}={$parentId}";
        $data  = array(
            '_parent_id' => $parentId,
            '_url'       => $route,
        );

        return $visitor->visitArray($data, $type, $context);
    }
}
