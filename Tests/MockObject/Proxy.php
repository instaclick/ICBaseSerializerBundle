<?php
/**
 * @copyright 2013 Instaclick Inc.
 */

namespace IC\Bundle\Base\SerializerBundle\Tests\MockObject;

use Doctrine\ORM\Proxy\Proxy as ProxyInterface;

/**
 * Dummy Entity Proxy (no behavior or logic)
 *
 * This class mimics the proxy class generated by Doctrine.
 *
 * @author Juti Noppornpitak <jutin@nationalfibre.net>
 */
class Proxy extends CustomEntity implements ProxyInterface
{
    /**
     * @var array properties to be lazy loaded, with keys being the property
     *            names and values being their default values
     *
     * @see \Doctrine\Common\Persistence\Proxy::__getLazyProperties
     */
    public static $lazyPropertiesDefaults = array();

    /**
     * Constructor
     *
     * @param \Closure $initializer
     * @param \Closure $cloner
     */
    public function __construct($initializer = null, $cloner = null)
    {
        $this->__initializer__ = $initializer;
        $this->__cloner__      = $cloner;
    }

    /**
     * Sleep
     *
     * @return array
     */
    public function __sleep()
    {
        throw new \RuntimeException('not implemented');
    }

    /**
     * Wake up
     */
    public function __wakeup()
    {
        throw new \RuntimeException('not implemented');
    }

    /**
     * Clone callback
     */
    public function __clone()
    {
        throw new \RuntimeException('not implemented');
    }

    /**
     * Forces initialization of the proxy
     */
    public function __load()
    {
        throw new \RuntimeException('not implemented');
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __isInitialized()
    {
        return $this->__isInitialized__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitialized($initialized)
    {
        $this->__isInitialized__ = $initialized;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitializer(\Closure $initializer = null)
    {
        $this->__initializer__ = $initializer;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __getInitializer()
    {
        return $this->__initializer__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setCloner(\Closure $cloner = null)
    {
        throw new \RuntimeException('not implemented');
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific cloning logic
     */
    public function __getCloner()
    {
        return $this->__cloner__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     * @static
     */
    public function __getLazyProperties()
    {
        return self::$lazyPropertiesDefaults;
    }
}
