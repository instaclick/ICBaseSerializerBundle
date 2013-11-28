<?php
/**
 * @copyright 2013 Instaclick Inc.
 */

namespace IC\Bundle\Base\SerializerBundle\Tests\Serializer\Handler;

use IC\Bundle\Base\SerializerBundle\Serializer\Handler\ClosureHandler;
use JMS\Serializer\GraphNavigator as GraphNavigator;
use IC\Bundle\Base\TestBundle\Test\TestCase;

/**
 * Test the closure handler
 *
 * @group Unit
 * @group Handler
 * @group ICBaseSerializerBundle
 *
 * @author Enzo Rizzo <enzor@nationalfibre.net>
 */
class ClosureHandlerTest extends TestCase
{
    /**
     * @var \IC\Bundle\Base\SerializerBundle\Serializer\Handler\ClosureHandler
     */
    private $closureHandler;

    /**
     * Setup the test
     */
    protected function setUp()
    {
        parent::setUp();

        $this->closureHandler = new ClosureHandler();
    }

    /**
     * Test all the possible scenarios for the getSubscribingMethods
     */
    public function testGetSubscribingMethods()
    {
        $subscribingMethods = $this->closureHandler->getSubscribingMethods();

        $this->assertArrayHasKey('direction', $subscribingMethods[0]);
        $this->assertArrayHasKey('format', $subscribingMethods[0]);
        $this->assertArrayHasKey('type', $subscribingMethods[0]);
        $this->assertArrayHasKey('method', $subscribingMethods[0]);

        $this->assertEquals(GraphNavigator::DIRECTION_SERIALIZATION, $subscribingMethods[0]['direction']);
        $this->assertEquals('json', $subscribingMethods[0]['format']);
        $this->assertEquals('Closure', $subscribingMethods[0]['type']);
        $this->assertEquals('skipClosure', $subscribingMethods[0]['method']);
    }

    /**
     * Test the only scenario for SkipClosure
     */
    public function testSkipClosure()
    {
        $visitorInterface = $this->createMock('JMS\Serializer\VisitorInterface');
        $type             = array();
        $context          = $this->createMock('JMS\Serializer\Context');
        $closure          = function () {
        };

        $this->assertNull($this->closureHandler->skipClosure($visitorInterface, $closure, $type, $context));
    }
}
