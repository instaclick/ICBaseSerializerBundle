<?php
/**
 * @copyright 2013 Instaclick Inc.
 */

namespace IC\Bundle\Base\SerializerBundle\Tests\Serializer\Handler;

use IC\Bundle\Base\TestBundle\Test\TestCase;
use IC\Bundle\Base\SerializerBundle\Serializer\Handler\ArrayCollectionHandler;
use Doctrine\ORM\PersistentCollection;
use JMS\Serializer\SerializationContext;
use IC\Bundle\Base\SerializerBundle\Entity\ProxyHandler as ProxyHandlerFlag;

/**
 * Array Collection Handler Test
 *
 * @group Unit
 * @group Handler
 * @group ICBaseSerializerBundle
 *
 * @author Enzo Rizzo <enzor@nationalfibre.net>
 */
class ArrayCollectionHandlerTest extends TestCase
{
    /**
     * @var \IC\Bundle\Base\SerializerBundle\Serializer\Handler\ArrayCollectionHandler
     */
    private $arrayCollectionHandler;

    /**
     * Setup test. Initialize the tested class
     */
    protected function setUp()
    {
        parent::setUp();

        $this->arrayCollectionHandler = new ArrayCollectionHandler();
    }

    /**
     * Test the class with different Doctrine collections
     *
     * @param mix     $collection
     * @param integer $order
     *
     * @dataProvider dataProviderForTestSerializer
     */
    public function testSerializeCollection($collection, $order)
    {
        $visitor    = $this->createAbstractMock('JMS\Serializer\AbstractVisitor');
        $router     = $this->createMock('IC\Bundle\Base\SecurityBundle\Routing\Router');
        $context    = $this->createContextMock(ProxyHandlerFlag::ENABLE_HANDLER);
        $type       = array('');

        3 === $order ? $returnValue = array(array('_route')) : $returnValue = array(1, 2, 3);

        $visitor
            ->expects($this->once())
            ->method('visitArray')
            ->with($this->anything())
            ->will($this->returnValue($returnValue));

        $this->arrayCollectionHandler->setRouter($router);

        $handler = $this->arrayCollectionHandler->serializeCollection($visitor, $collection, $type, $context);

        $this->assertEquals($returnValue, $handler);

    }

    /**
     * Provide the different cases for the collection handler
     *
     * @return array
     */
    public function dataProviderForTestSerializer()
    {
        return array (
            array ($this->createMock('Doctrine\Common\Collections\Collection'), 1),
            array ($this->createPersistentCollection(), 2),
            array ($this->createPersistentCollection('parent'), 3),
        );
    }

    /**
     * Test the exception throw when there is not parent mapping information
     *
     * @expectedException \IC\Bundle\Base\SerializerBundle\Exception\NoMappingException
     */
    public function testSerializeCollectionThrowException()
    {
        $collection  = $this->createPersistentCollectionThrowException();
        $visitor     = $this->createAbstractMock('JMS\Serializer\AbstractVisitor');
        $context     = $this->createContextMock(ProxyHandlerFlag::ENABLE_HANDLER);
        $type        = array();

        $this->arrayCollectionHandler->serializeCollection($visitor, $collection, $type, $context);
    }

    /**
     * Create the scenario to throw the exception
     *
     * @return \Doctrine\ORM\PersistentCollection
     */
    private function createPersistentCollectionThrowException()
    {
        $em    = $this->createMock('Doctrine\ORM\EntityManager');
        $class = $this->createMock('Doctrine\ORM\Mapping\ClassMetadata');
        $coll  = array();

        $persistentCollection = new PersistentCollection($em, $class, $coll);

        $entity =  $this->getHelper('Unit/Entity')->createMock('Doctrine\ORM\Entity', 1);

        $entityAssociationMapping = array(
            'inversedBy' => '',
            'mappedBy' => '',
        );

        $parentAssociationMappings  = array(
            'dependantChildList' => array(
                'inversedBy'   => 'parent',
                'targetEntity' => 'Parent',
            )
        );

        $persistentCollection->setOwner($entity, $entityAssociationMapping);
        $persistentCollection->setInitialized(false);

        $class
            ->expects($this->once())
            ->method('getAssociationMappings')
            ->will($this->returnValue($parentAssociationMappings));

        $persistentCollection->getTypeClass();

        return $persistentCollection;
    }

    /**
     * Create the different scenarios of the collection handler
     *
     * @param string $inversedBy
     *
     * @return \Doctrine\ORM\PersistentCollection
     */
    private function createPersistentCollection($inversedBy = null)
    {
        $em    = $this->createMock('Doctrine\ORM\EntityManager');
        $class = $this->createMock('Doctrine\ORM\Mapping\ClassMetadata');
        $coll  = array();

        $persistentCollection = new PersistentCollection($em, $class, $coll);

        $entity = $this->getHelper('Unit/Entity')->createMock('Doctrine\ORM\Entity', 1);
        $className = get_class($entity);

        $entityAssociationMapping = array(
            'inversedBy' => '',
            'mappedBy'   => '',
        );

        $parentAssociationMappings = array(
            'dependantChildList' => array(
                'inversedBy'   => $inversedBy,
                'targetEntity' => $className,
            )
        );

        $persistentCollection->setOwner($entity, $entityAssociationMapping);
        $persistentCollection->setInitialized(false);

        $class
            ->expects($this->once())
            ->method('getAssociationMappings')
            ->will($this->returnValue($parentAssociationMappings));

        $class->name = '\IC\Bundle\Base\SerializerBundle\Entity\ProxyHandler';

        $persistentCollection->getTypeClass();

        return $persistentCollection;
    }

    /**
     * Create a context mock
     *
     * @param mixed $returnValue
     *
     * @return \JMS\Serializer\Context $contextMock
     */
    private function createContextMock($returnValue)
    {
        $mapMock = $this->createMock('PhpCollection\Map');
        $mapMock->expects($this->any())
            ->method('get')
            ->will($this->returnValue($returnValue));

        $contextMock             = new SerializationContext();
        $contextMock->attributes = $mapMock;

        return $contextMock;
    }
}
