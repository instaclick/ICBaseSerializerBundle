<?php
/**
 * @copyright 2013 Instaclick Inc.
 */

namespace IC\Bundle\Base\SerializerBundle\Tests\Serializer\Handler;

use IC\Bundle\Base\SerializerBundle\Serializer\Handler\ProxyHandler;
use IC\Bundle\Base\TestBundle\Test\TestCase;
use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\SerializationContext;
use IC\Bundle\Base\SerializerBundle\Tests\MockObject\Context;
use IC\Bundle\Base\SerializerBundle\Tests\MockObject\Entity;
use IC\Bundle\Base\SerializerBundle\Tests\MockObject\Proxy;
use PhpOption\None;

/**
 * Proxy Handler Test
 *
 * @group Unit
 * @group Handler
 * @group ICBaseSerializerBundle
 *
 * @author Kinn Coelho Julião <kinnj@nationalfibre.net>
 * @author Juti Noppornpitak <jutin@nationalfibre.net>
 */
class ProxyHandlerTest extends TestCase
{
    /**
     * @var \IC\Bundle\Base\SerializerBundle\Serializer\Handler\ProxyHandler
     */
    private $handler;

    /**
     * {@inherit}
     */
    public function setUp()
    {
        if ( ! class_exists('JMS\Serializer\Exception\SkipStepException')) {
            $this->markTestSkipped(
                'This test cannot run without the new JMS Serializer.'
            );
        }

        $this->handler = new ProxyHandler();
        $this->handler->setRouter($this->createRouterMock());

        parent::setUp();
    }

    /**
     * Should skip the serializer when the root of the visitor is null
     *
     * @expectedException JMS\Serializer\Exception\SkipStepException
     */
    public function testShouldSkipTheSerializerWithVisitorRootIsNull()
    {
        $visitorMock = $this->createVisitorMock(null);
        $entityMock  = $this->createEntityMock();
        $typeMock    = $this->createTypeMock();
        $contextMock = $this->createContextMock(None::create());

        $this->handler->serializeProxy($visitorMock, $entityMock, $typeMock, $contextMock);
    }

    /**
     * Should skip the serializer with the context
     *
     * @expectedException JMS\Serializer\Exception\SkipStepException
     */
    public function testShouldSkipTheSerializerWithContext()
    {
        $visitorMock = $this->createVisitorMock(new \stdClass());
        $entityMock  = $this->createEntityMock();
        $typeMock    = $this->createTypeMock();
        $contextMock = $this->createContextMock(None::create());

        $this->handler->serializeProxy($visitorMock, $entityMock, $typeMock, $contextMock);
    }

    /**
     * Should skip the serializer with the initialized proxy
     *
     * @expectedException JMS\Serializer\Exception\SkipStepException
     */
    public function testShouldSkipTheSerializerWithInitializedProxy()
    {
        $visitorMock = $this->createVisitorMock(new \stdClass());
        $proxyMock   = $this->createProxyMock(true);
        $typeMock    = $this->createTypeMock();
        $contextMock = $this->createContextMock(false);

        $this->handler->serializeProxy($visitorMock, $proxyMock, $typeMock, $contextMock);
    }

    /**
     * Should skip the serializer with the non-proxy entity
     *
     * @expectedException JMS\Serializer\Exception\SkipStepException
     */
    public function testShouldSkipTheSerializerWithNonProxy()
    {
        $visitorMock = $this->createVisitorMock(new \stdClass());
        $proxyMock   = $this->createEntityMock();
        $typeMock    = $this->createTypeMock();
        $contextMock = $this->createContextMock(true);

        $this->handler->serializeProxy($visitorMock, $proxyMock, $typeMock, $contextMock);
    }

    /**
     * Should handle the serialization
     */
    public function testShouldHandleSerialization()
    {
        $entity      = $this->getHelper('Unit\Entity')->createMock('IC\Bundle\Core\UserBundle\Entity\User', 1);
        $visitorMock = $this->createVisitorMock($entity);
        $entityMock  = $entity;
        $typeMock    = $this->createTypeMock();
        $contextMock = $this->createContextMock(false);

        $expectedResult = array("id" => 1, "_url" => "http://localhost/api/v1/core/foo/bar/1");
        $actualResult   = $this->handler->serializeProxy($visitorMock, $entityMock, $typeMock, $contextMock);

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * Test the subscribed methods
     */
    public function testSubscribedMethods()
    {
        $expectedResult = array(
            array(
                'direction' => 1,
                'format'    => 'json',
                'type'      => 'IC\Bundle\Base\ComponentBundle\Entity\Entity',
                'method'    => 'serializeProxy',
            ),
        );

        $actualResult = $this->handler->getSubscribingMethods();

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * Create a visitor mock
     *
     * @param \stdClass|null $root the mocked root
     *
     * @return \JMS\Serializer\JsonSerializationVisitor $visitorMock
     */
    private function createVisitorMock($root)
    {
        $visitorMock = $this->createMock('JMS\Serializer\JsonSerializationVisitor');
        $visitorMock->expects($this->any())
            ->method('getRoot')
            ->will($this->returnValue($root));

        return $visitorMock;
    }

    /**
     * Create an entity mock
     *
     * @return \IC\Bundle\Base\ComponentBundle\Entity\Entity $entityMock
     */
    private function createEntityMock()
    {
        $entityMock = new Entity;
        $entityMock->setId(1);

        return $entityMock;
    }

    /**
     * Create a type stub
     *
     * @return array
     */
    private function createTypeMock()
    {
        return array('name' => 'Ic\Bundle\Core\FooBundle\Entity\FooBarBazz');
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

    /**
     * Create a proxy mock
     *
     * @param boolean $isInitialized the flag to fake Proxy::__isInitialized()
     *
     * @return \Doctrine\ORM\Proxy\Proxy $proxyMock
     */
    private function createProxyMock($isInitialized)
    {
        $proxyMock = new Proxy();

        $proxyMock->__setInitialized($isInitialized);
        $proxyMock->setId(1);

        return $proxyMock;
    }

    /**
     * Create a router mock
     *
     * @return \Symfony\Component\Routing\Router $proxyMock
     */
    private function createRouterMock()
    {
        $proxyMock = $this->createMock('IC\Bundle\Core\SecurityBundle\Routing\Router');
        $proxyMock->expects($this->any())
            ->method('generate')
            ->will($this->returnValue('http://localhost/api/v1/core/foo/bar/1'));

        return $proxyMock;
    }
}
