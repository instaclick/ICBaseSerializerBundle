<?php
/**
 * @copyright 2013 Instaclick Inc.
 */

namespace IC\Bundle\Base\SerializerBundle\Service;

use JMS\Serializer\Serializer;
use JMS\Serializer\SerializationContext;

/**
 * SerializerService
 *
 * @author Danilo Cabello <daniloc@nationalfibre.net>
 */
class SerializerService extends Serializer
{
    /**
     * Override method to create default context with default options.
     *
     * @param mixed                                $data
     * @param string                               $format
     * @param \JMS\Serializer\SerializationContext $context
     *
     * @return string
     */
    public function serialize($data, $format, SerializationContext $context = null)
    {
        if (null === $context) {
            $context = SerializationContext::create()->setSerializeNull(true)->enableMaxDepthChecks();
        }

        return parent::serialize($data, $format, $context);
    }
}
