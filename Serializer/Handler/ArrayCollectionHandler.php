<?php
namespace IC\Bundle\Base\SerializerBundle\Serializer\Handler;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\PersistentCollection;
use IC\Bundle\Base\SecurityBundle\Routing\Router;
use IC\Bundle\Base\SerializerBundle\Entity\ProxyHandler as ProxyHandlerFlag;
use IC\Bundle\Base\SerializerBundle\Exception\NoMappingException;
use JMS\Serializer\Context;
use JMS\Serializer\Handler\ArrayCollectionHandler as BaseArrayCollectionHandler;
use JMS\Serializer\VisitorInterface;
use PhpOption\None;

/**
 * This handler prevent to load a collectio and provide the REST Api Filter entrypoint for it
 *
 * @author Kinn Coelho Julião <kinnj@nationalfibre.net>
 */
class ArrayCollectionHandler extends BaseArrayCollectionHandler
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
     * {@inheritdoc}
     */
    public function serializeCollection(VisitorInterface $visitor, Collection $collection, array $type, Context $context)
    {
        $flag = $context->attributes->get(ProxyHandlerFlag::ENABLE_HANDLER);

        if (($flag instanceof None) || ( ! $collection instanceof PersistentCollection) || $collection->isInitialized()) {
            return $visitor->visitArray($collection, $type, $context);
        }

        $classMetadata  = $collection->getTypeClass();
        $associationMap = $classMetadata->getAssociationMappings();
        $className      = $classMetadata->name;

        $parent          = $collection->getOwner();
        $parentClassName = get_class($parent);

        $mappingInfo = $this->getParentMappingInformation($associationMap, $parentClassName);

        if (empty($mappingInfo['inversedBy'])) {
            return $visitor->visitArray($collection, $type, $context);
        }

        $fieldName = $mappingInfo['fieldName'];
        $parentId  = $parent->getId();
        $route     = $this->constructUrlToRestApi($fieldName, $className, $parentId);

        $data = array(
            '_url' => $route,
        );

        return $visitor->visitArray($data, $type, $context);
    }

    /**
     * Construct the URL to the corresponding endpoint (REST API)
     *
     * @param string         $fieldName the name of the field
     * @param string         $className the fully qualified class name
     * @param string|integer $entityId  the identifier of the entity
     *
     * @return string
     */
    private function constructUrlToRestApi($fieldName, $className, $entityId)
    {
        $typePartList = explode('\\', $className);

        $parameterList = array(
            'packageName'    => strtolower($typePartList[2]),
            'subPackageName' => strtolower(substr($typePartList[3], 0, strpos($typePartList[3], 'Bundle'))),
            'entityName'     => strtolower($typePartList[5]),
        );

        $route = $this->router->generate('ICBaseRestBundle_Rest_Filter', $parameterList, Router::ABSOLUTE_URL);

        return sprintf('%s?%s=%s', $route, $fieldName, $entityId);
    }

    /**
     * Get the mapping information
     *
     * @param array  $associationMap the name of the field
     * @param string $className      the fully qualified class name
     *
     * @return string
     *
     * @throws \IC\Bundle\Base\SerializerBundle\Exception\NoMappingException when the parent mapping is not available.
     */
    private function getParentMappingInformation(array $associationMap, $className)
    {
        foreach ($associationMap as $fieldName => $mappingInfo) {
            if ($mappingInfo['targetEntity'] !== $className) {
                continue;
            }

            return array(
                'fieldName'  => $fieldName,
                'inversedBy' => $mappingInfo['inversedBy'],
            );
        }

        throw new NoMappingException('Cannot find the parent mapping information for ' . $className);
    }
}
