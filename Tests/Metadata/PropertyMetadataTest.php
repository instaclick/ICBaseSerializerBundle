<?php
/**
 * @copyright 2013 Instaclick Inc.
 */

namespace IC\Bundle\Base\SerializerBundle\Tests\Metadata;

use IC\Bundle\Base\SerializerBundle\Metadata\PropertyMetadata;
use IC\Bundle\Base\TestBundle\Test\TestCase;

/**
 * Serializer Service Test
 *
 * @group Unit
 * @group ICBaseSerializerBundle
 *
 * @author David Maignan <davidm@nationalfibre.net>
 */
class PropertyMetadataTest extends TestCase
{
    /**
     * @var \IC\Bundle\Base\SerializerBundle\Metadata\PropertyMetadata
     */
    private $propertyMetadata;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $entity = $this->getHelper('Unit\Entity')->createMock('IC\Bundle\Base\SerializerBundle\Tests\MockObject\Entity', 1);

        $this->propertyMetadata = new PropertyMetadata($entity, 'name');
    }

    /**
     * Test isTranslatable method
     */
    public function testIsTranslatable()
    {
        $this->assertFalse($this->propertyMetadata->isTranslatable());
    }

    /**
     * Test setTranslatable method
     */
    public function testSetTranslatable()
    {
        $this->propertyMetadata->setTranslatable();

        $this->assertTrue($this->propertyMetadata->isTranslatable());
    }
}
