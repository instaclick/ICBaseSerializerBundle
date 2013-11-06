<?php
/**
 * @copyright 2013 Instaclick Inc.
 */

namespace IC\Bundle\Base\SerializerBundle\Tests\MockObject;

/**
 * Dummy Type (no behavior or logic)
 *
 * @author Anthon Pang <anthonp@nationalfibre.net>
 */
class Type
{
    protected $type;

    /**
     * Get type
     *
     * @return $mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set type
     *
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }
}
