<?php
/**
 * @copyright 2013 Instaclick Inc.
 */

namespace IC\Bundle\Base\SerializerBundle\Tests\Metadata\Driver;

use IC\Bundle\Base\SerializerBundle\Metadata\Driver\AnnotationDriver;
use IC\Bundle\Base\TestBundle\Test\TestCase;

/**
 * Annotation Driver Test
 *
 * @group Unit
 * @group ICBaseSerializerBundle
 *
 * @author David Maignan <davidm@nationalfibre.net>
 */
class AnnotationDriverTest extends TestCase
{
    /**
     * Test loadMetaDataForClass method
     */
    public function testLoadMetadataForClass()
    {
        $reader = $this->createMock('Doctrine\Common\Annotations\AnnotationReader');
        $entity = $this->getHelper('Unit\Entity')->createMock('IC\Bundle\Base\SerializerBundle\Tests\MockObject\Entity', 1);

        $annotationDriver = new AnnotationDriver($reader);

        $reader->expects($this->at(0))
            ->method('getPropertyAnnotation')
            ->will($this->returnValue(null));

        $reader->expects($this->at(1))
            ->method('getPropertyAnnotation')
            ->will($this->returnValue('Translatable'));

        $result = $annotationDriver->loadMetadataForClass(new \ReflectionClass(get_parent_class($entity)));

        $this->assertEquals(false, $result->propertyMetadata["id"]->isTranslatable());
        $this->assertEquals(true, $result->propertyMetadata["name"]->isTranslatable());
    }
}
