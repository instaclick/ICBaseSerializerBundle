<?php
/**
 * @copyright 2013 Instaclick Inc.
 */

namespace IC\Bundle\Base\SerializerBundle\Tests\MockObject;

use IC\Bundle\Base\ComponentBundle\Entity\Entity as BaseEntity;

/**
 * Dummy Entity (no behavior or logic)
 *
 * @author Juti Noppornpitak <jutin@nationalfibre.net>
 */
class CustomEntity extends BaseEntity
{
    protected $id;

    protected $name;

    /**
     * Get ID
     *
     * @return $mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set ID
     *
     * @param mixed $id
     */
    public function setID($id)
    {
        $this->id = $id;
    }

    /**
     * Set name
     *
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }
}
