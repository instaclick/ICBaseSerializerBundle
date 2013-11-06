<?php
/**
 * @copyright 2013 Instaclick Inc.
 */

namespace IC\Bundle\Base\SerializerBundle\Tests\EventListener\Serializer;

use IC\Bundle\Base\SerializerBundle\EventListener\Serializer\SerializeListener;
use IC\Bundle\Base\TestBundle\Test\TestCase;
use JMS\Serializer\SerializationContext;
use Metadata\AdvancedMetadataFactoryInterface;
use Metadata\ClassMetadata;

/**
 * Serialize Listener Test
 *
 * @group ICBaseSerializerBundle
 * @group Unit
 * @group EventListener
 *
 * @author David Maignan <davidm@nationalfibre.net>
 */
class SerializeListenerTest extends TestCase
{
    /**
     * @var \IC\Bundle\Base\SerializerBundle\EventListener\Serializer\SerializeListener
     */
    private $listener;

    /**
     * @var \Symfony\Component\Translation\TranslatorInterface
     */
    private $translator;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * @var \Metadata\AdvancedMetadataFactoryInterface
     */
    private $metadataFactory;

    /**
     * @var \JMS\Serializer\SerializationContext
     */
    private $context;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->translator      = $this->createMock('Symfony\Component\Translation\TranslatorInterface');
        $this->metadataFactory = $this->createMetadataFactoryMock();
        $this->entityManager   = $this->createMock('Doctrine\ORM\EntityManager');
        $this->listener        = new SerializeListener();

        $this->listener->setTranslator($this->translator);
        $this->listener->setMetadataFactory($this->metadataFactory);
        $this->listener->setEntityManager($this->entityManager);
    }

    /**
     * Test subscribedEvents
     */
    public function testGetSubscribedEvents()
    {
        $expected = array(
            array('event' => 'serializer.pre_serialize', 'method' => 'onPreSerialize'),
            array('event' => 'serializer.post_serialize', 'method' => 'onPostSerialize'));

        $this->assertEquals($expected, SerializeListener::getSubscribedEvents());
    }

    /**
     * Test onPreSerialize without translatable key
     */
    public function testOnPreSerializeContainsNoKeyTranslatable()
    {
        $event   = $this->createMockEvent('PreSerializeEvent');
        $context = new SerializationContext();

        $event->expects($this->once())
            ->method('getContext')
            ->will($this->returnValue($context));

        $event->expects($this->never())
            ->method('getObject');

        $this->listener->onPreSerialize($event);
    }

    /**
     * Test onPreSerialize with translatable key null
     */
    public function testOnPreSerializeContainsKeyTranslatableNull()
    {
        $event   = $this->createMockEvent('PreSerializeEvent');
        $context = new SerializationContext();

        $context->attributes->set('translatable', null);

        $event->expects($this->once())
            ->method('getContext')
            ->will($this->returnValue($context));

        $event->expects($this->never())
            ->method('getObject');

        $this->listener->onPreSerialize($event);
    }

    /**
     * Test on preSerialize with no metaData translatable
     */
    public function testOnPreSerializeNoMetaDataTranslatable()
    {
        $event   = $this->createMockEvent('PreSerializeEvent');
        $context = new SerializationContext();

        $context->attributes->set('translatable', 'mock value');

        $classMetaData = $this->createMock('Metadata\ClassMetadata');
        $entity        = $this->getHelper('Unit\Entity')->createMock('IC\Bundle\Base\SerializerBundle\Tests\MockObject\Entity', 1);

        $event->expects($this->once())
            ->method('getContext')
            ->will($this->returnValue($context));

        $event->expects($this->once())
            ->method('getObject')
            ->will($this->returnValue($entity));

        $this->metadataFactory->expects($this->once())
            ->method('getMetadataForClass')
            ->with(get_class($entity))
            ->will($this->returnValue($classMetaData));

        $this->translator->expects($this->never())
            ->method('trans');

        $this->listener->onPreSerialize($event);
    }

    /**
     * Test onPreSerialize with one metadata translatable
     */
    public function testOnPreSerializeWithMetaDataTranslatable()
    {
        $event   = $this->createMockEvent('PreSerializeEvent');
        $context = new SerializationContext();

        $context->attributes->set('translatable', 'mock value');

        $entity             = $this->getHelper('Unit\Entity')->createMock('IC\Bundle\Base\SerializerBundle\Tests\MockObject\Entity', 1);
        $classMetadata      = new ClassMetadata($entity);
        $propertyMetadataId = $this->getMockBuilder('IC\Bundle\Base\SerializerBundle\Metadata\PropertyMetadata')
            ->enableOriginalConstructor()
            ->setConstructorArgs(array($entity, 'id'))
            ->setMethods(array('isTranslatable', 'setValue', 'getValue'))
            ->getMock();

        $propertyMetadataName = $this->getMockBuilder('IC\Bundle\Base\SerializerBundle\Metadata\PropertyMetadata')
            ->enableOriginalConstructor()
            ->setConstructorArgs(array($entity, 'name'))
            ->setMethods(array('isTranslatable'))
            ->getMock();

        $entity->setID(1);

        $classMetadata->addPropertyMetadata($propertyMetadataId);
        $classMetadata->addPropertyMetadata($propertyMetadataName);

        $propertyMetadataId->expects($this->once())
            ->method('isTranslatable')
            ->will($this->returnValue(true));

        $propertyMetadataName->expects($this->once())
            ->method('isTranslatable')
            ->will($this->returnValue(false));

        $event->expects($this->once())
            ->method('getContext')
            ->will($this->returnValue($context));

        $event->expects($this->once())
            ->method('getObject')
            ->will($this->returnValue($entity));

        $this->metadataFactory->expects($this->once())
            ->method('getMetadataForClass')
            ->with(get_class($entity))
            ->will($this->returnValue($classMetadata));

        $this->translator->expects($this->once())
            ->method('trans')
            ->with($this->equalTo(1))
            ->will($this->returnValue('mock translated value'));

        $propertyMetadataId->expects($this->once())
            ->method('getValue')
            ->with($entity)
            ->will($this->returnValue(1));

        $propertyMetadataId->expects($this->once())
            ->method('setValue')
            ->with($entity, 'mock translated value');

        $this->listener->onPreSerialize($event);
    }

    /**
     * Test onPostSerialize with translatable key null
     */
    public function testOnPostSerializeContainsKeyTranslatableNull()
    {
        $event   = $this->createMockEvent();
        $context = new SerializationContext();

        $context->attributes->set('translatable', null);

        $event->expects($this->once())
            ->method('getContext')
            ->will($this->returnValue($context));

        $event->expects($this->never())
            ->method('getObject');

        $this->listener->onPostSerialize($event);
    }

    /**
     * Test on postSerialize with no metaData translatable
     */
    public function testOnPostSerializeNoMetaDataTranslatable()
    {
        $event   = $this->createMockEvent();
        $context = new SerializationContext();

        $context->attributes->set('translatable', 'mock value');

        $classMetaData = $this->createMock('Metadata\ClassMetadata');
        $entity        = $this->getHelper('Unit\Entity')->createMock('IC\Bundle\Base\SerializerBundle\Tests\MockObject\Entity', 1);

        $event->expects($this->once())
            ->method('getContext')
            ->will($this->returnValue($context));

        $event->expects($this->once())
            ->method('getObject')
            ->will($this->returnValue($entity));

        $this->metadataFactory->expects($this->once())
            ->method('getMetadataForClass')
            ->with(get_class($entity))
            ->will($this->returnValue($classMetaData));

        $this->entityManager->expects($this->never())
            ->method('refresh');

        $this->listener->onPostSerialize($event);
    }

    /**
     * Test onPreSerialize with one metadata translatable
     */
    public function testOnPostSerializeWithMetaDataTranslatable()
    {
        $event   = $this->createMockEvent('PreSerializeEvent');
        $context = new SerializationContext();

        $context->attributes->set('translatable', 'mock value');

        $entity             = $this->getHelper('Unit\Entity')->createMock('IC\Bundle\Base\SerializerBundle\Tests\MockObject\Entity', 1);
        $classMetadata      = new ClassMetadata($entity);
        $propertyMetadataId = $this->getMockBuilder('IC\Bundle\Base\SerializerBundle\Metadata\PropertyMetadata')
            ->enableOriginalConstructor()
            ->setConstructorArgs(array($entity, 'id'))
            ->setMethods(array('isTranslatable', 'setValue', 'getValue'))
            ->getMock();

        $propertyMetadataName = $this->getMockBuilder('IC\Bundle\Base\SerializerBundle\Metadata\PropertyMetadata')
            ->enableOriginalConstructor()
            ->setConstructorArgs(array($entity, 'name'))
            ->setMethods(array('isTranslatable'))
            ->getMock();

        $entity->setID(1);

        $classMetadata->addPropertyMetadata($propertyMetadataName);
        $classMetadata->addPropertyMetadata($propertyMetadataId);

        $propertyMetadataId->expects($this->once())
            ->method('isTranslatable')
            ->will($this->returnValue(true));

        $propertyMetadataName->expects($this->once())
            ->method('isTranslatable')
            ->will($this->returnValue(false));

        $event->expects($this->once())
            ->method('getContext')
            ->will($this->returnValue($context));

        $event->expects($this->once())
            ->method('getObject')
            ->will($this->returnValue($entity));

        $this->metadataFactory->expects($this->once())
            ->method('getMetadataForClass')
            ->with(get_class($entity))
            ->will($this->returnValue($classMetadata));

        $this->entityManager->expects($this->once())
            ->method('refresh')
            ->with($this->equalTo($entity));

        $this->listener->onPostSerialize($event);
    }

    /**
     * Create a mock event
     *
     * @param string $type Define the type of event
     *
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    private function createMockEvent($type = 'PostSerializeEvent')
    {
        if ('PreSerializeEvent' == $type) {
            return $this->createMock('JMS\Serializer\EventDispatcher\PreSerializeEvent');
        }

        return $this->createMock('JMS\Serializer\EventDispatcher\ObjectEvent');
    }

    /**
     * Create a metadataFactory mock
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function createMetadataFactoryMock()
    {
        return $this->getMockBuilder('Metadata\AdvancedMetadataFactoryInterface')
                ->setMethods(array(
                    'getMetadataForClass',
                    'getAllClassNames'
                ))->getMock();
    }
}
