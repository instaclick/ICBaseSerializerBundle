<?php
/**
 * @copyright 2013 Instaclick Inc.
 */

namespace IC\Bundle\Base\SerializerBundle\Tests\DependencyInjection;

use IC\Bundle\Base\SerializerBundle\DependencyInjection\ICBaseSerializerExtension;
use IC\Bundle\Base\TestBundle\Test\DependencyInjection\ExtensionTestCase;

/**
 * Test for ICBaseSerializerExtension
 *
 * @group ICBaseSerializerBundle
 * @group Unit
 * @group DependencyInjection
 *
 * @author John Zhang <johnz@nationalfibre.net>
 */
class ICBaseSerializerExtensionTest extends ExtensionTestCase
{
    /**
     * Test configuration
     */
    public function testConfiguration()
    {
        $loader = new ICBaseSerializerExtension();

        $this->load($loader, array());
    }
}
