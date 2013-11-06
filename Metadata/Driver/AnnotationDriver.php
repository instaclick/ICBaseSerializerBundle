<?php
/**
 * @copyright 2013 Instaclick Inc.
 */

namespace IC\Bundle\Base\SerializerBundle\Metadata\Driver;

use Doctrine\Common\Annotations\Reader;
use IC\Bundle\Base\SerializerBundle\Metadata\PropertyMetadata;
use Metadata\Driver\DriverInterface;
use Metadata\MergeableClassMetadata;

/**
 * Metadata Driver
 *
 * @author Yuan Xie <shayx@nationalfibre.net>
 */
class AnnotationDriver implements DriverInterface
{
    /**
     * @var \Doctrine\Common\Annotations\Reader
     */
    private $reader;

    /**
     * Constructor
     *
     * @param \Doctrine\Common\Annotations\Reader $reader reader
     */
    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * Load metadata for class
     *
     * @param \ReflectionClass $class class
     *
     * @return mixed
     */
    public function loadMetadataForClass(\ReflectionClass $class)
    {
        $classMetadata = new MergeableClassMetadata($class->getName());

        foreach ($class->getProperties() as $reflectionProperty) {
            $propertyMetadata = new PropertyMetadata($class->getName(), $reflectionProperty->getName());

            $annotation = $this->reader->getPropertyAnnotation(
                $reflectionProperty,
                'IC\\Bundle\\Base\\SerializerBundle\\Annotation\\Translatable'
            );

            // "@Translatable" annotation was found
            if (null !== $annotation) {
                $propertyMetadata->setTranslatable();
            }

            $classMetadata->addPropertyMetadata($propertyMetadata);
        }

        return $classMetadata;
    }
}
