<?php
/**
 * @copyright 2013 Instaclick Inc.
 */

namespace IC\Bundle\Base\SerializerBundle\Metadata;

use Metadata\PropertyMetadata as BasePropertyMetadata;

/**
 * Property Metadata
 *
 * @author Yuan Xie <shayx@nationalfibre.net>
 */
class PropertyMetadata extends BasePropertyMetadata
{
    /**
     * @var boolean
     */
    private $translatable = false;

    /**
     * Whether or not the property is translatable.
     *
     * @return boolean
     */
    public function isTranslatable()
    {
        return $this->translatable;
    }

    /**
     * Set the property's state of translatable.
     *
     * @param boolean $translatable
     */
    public function setTranslatable($translatable = true)
    {
        $this->translatable = $translatable;
    }
}
