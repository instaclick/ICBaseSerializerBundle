<?php
namespace IC\Bundle\Base\SerializerBundle\Serializer\Handler;

use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\VisitorInterface;
use Doctrine\Common\Collections\Collection;
use JMS\Serializer\Handler\ArrayCollectionHandler as BaseArrayCollectionHandler;
use IC\Bundle\Core\SecurityBundle\Routing\Router;
use Doctrine\ORM\PersistentCollection;
use PhpOption\None;

/**
 * This handler prevent to load a collectio and provide the REST Api Filter entrypoint for it
 *
 * @author Kinn Coelho Julião <kinnj@nationalfibre.net>
 */
class ArrayCollectionHandler extends BaseArrayCollectionHandler
{
    const SKIP_HANDLER   = 'skip_json_handler';
    const ENABLE_HANDLER = 'enable_json_handler';

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
    public function serializeCollection(VisitorInterface $visitor, Collection $collection, array $type, Context $context)
    {
        $flag = $context->attributes->get(self::ENABLE_HANDLER);

        if (($flag instanceof None) || ( ! $collection instanceof PersistentCollection) || $collection->isInitialized()) {
            return $visitor->visitArray($collection, $type, $context);
        }

        $classMetadata  = $collection->getTypeClass();
        $associationMap = $classMetadata->getAssociationMappings();
        $className      = $classMetadata->name;

        $parent                = $collection->getOwner();
        $actualParentClassName = get_class($parent);

        $hasReverseMapping = false;

        foreach ($associationMap as $fieldName => $mappingInfo) {
            if ($mappingInfo['targetEntity'] !== $actualParentClassName) {
                continue;
            }

            $hasReverseMapping = ! empty($mappingInfo['inversedBy']);

            break;
        }

        if ( ! $hasReverseMapping) {
            return $visitor->visitArray($collection, $type, $context);
        }

        $parentClassPartList = explode('\\', $actualParentClassName);
        $parentClassName     = lcfirst($parentClassPartList[(count($parentClassPartList) - 1)]);
        $parentId            = $parent->getId();
        $typePartList        = explode('\\', $className);
        $parameterList       = array(
            'packageName'    => strtolower($typePartList[2]),
            'subPackageName' => strtolower(substr($typePartList[3], 0, strpos($typePartList[3], 'Bundle'))),
            'entityName'     => strtolower($typePartList[5]),
        );

        $route = $this->router->generate('ICBaseRestBundle_Rest_Filter', $parameterList, Router::ABSOLUTE_URL);
        $route = "{$route}?{$parentClassName}={$parentId}";
        $data  = array(
            '_url' => $route,
        );

        return $visitor->visitArray($data, $type, $context);
    }
}
