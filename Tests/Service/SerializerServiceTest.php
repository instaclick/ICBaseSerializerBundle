<?php
/**
 * @copyright 2013 Instaclick Inc.
 */

namespace IC\Bundle\Base\SerializerBundle\Tests\Service;

use IC\Bundle\Base\SerializerBundle\Service\SerializerService;
use IC\Bundle\Base\TestBundle\Test\TestCase;
use JMS\Serializer\GraphNavigator;

/**
 * Serializer Service Test
 *
 * @group Unit
 * @group Metadata
 *
 * @author Enzo Rizzo <enzor@nationalfibre.net>
 */
class SerializerServiceTest extends TestCase
{
    /**
     * @var \IC\Bundle\Base\SerializerBundle\Service\SerializerService
     */
    private $serializerService;

    /**
     * Set up test
     */
    protected function setUp()
    {
        parent::setUp();

        $factory                 = $this->createMock('Metadata\MetadataFactoryInterface');
        $handlerRegistry         = $this->createMock('JMS\Serializer\Handler\HandlerRegistryInterface');
        $objectConstructor       = $this->createMock('JMS\Serializer\Construction\ObjectConstructorInterface');
        $abstractMap             = $this->createAbstractMock('PhpCollection\AbstractMap');
        $serializationVisitors   = $abstractMap;
        $deserializationVisitors = $abstractMap;
        $option                  = $this->createAbstractMock('PhpOption\Option');
        $visitor                 = $this->createMock('JMS\Serializer\VisitorInterface');

        $serializationVisitors
            ->expects($this->once())
            ->method('containsKey')
            ->with('json')
            ->will($this->returnValue(true));

        $serializationVisitors
            ->expects($this->once())
            ->method('get')
            ->will($this->returnValue($option));

        $option
            ->expects($this->once())
            ->method('get')
            ->will($this->returnValue($visitor));

        $this->serializerService = new SerializerService(
            $factory,
            $handlerRegistry,
            $objectConstructor,
            $serializationVisitors,
            $deserializationVisitors
        );
    }

    /**
     * Test the serializer service with context
     */
    public function testSerializerService()
    {
        $context   = $this->createMock('JMS\Serializer\SerializationContext');
        $visitor   = $this->createMock('JMS\Serializer\VisitorInterface');
        $data      = array('hello World' => 'Hello World 2');
        $format    = 'json';

        $context
            ->expects($this->once())
            ->method('getVisitor')
            ->will($this->returnValue($visitor));

        $this->serializerService->serialize($data, $format, $context);
    }

    /**
     * Test the serializer service with no context
     */
    public function testSerializerService2()
    {
        $data      = array();
        $format    = 'json';

        $this->serializerService->serialize($data, $format);
    }
}
