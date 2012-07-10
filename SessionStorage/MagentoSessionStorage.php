<?php

namespace Liip\MagentoBundle\SessionStorage;

use Symfony\Component\HttpFoundation\SessionStorage\SessionStorageInterface;

class MagentoSessionStorage implements SessionStorageInterface
{
    static protected $sessionIdRegenerated = false;
    static protected $sessionStarted       = false;

    /**
     * @var Mage_Core_Model_Session_Abstract
     */
    private $session;

    private $sessionNamespace;

    public function __construct($sessionNamespace = 'frontend')
    {
        $this->sessionNamespace = $sessionNamespace;
    }

    /**
     * {@inheritDoc}
     */
    public function start()
    {
        if (self::$sessionStarted) {
            return;
        }

        $this->session = \Mage::getSingleton('core/session', array('name' => $this->sessionNamespace));

        // start Magento session
        $this->session->start();

        self::$sessionStarted = true;
    }

    /**
     * {@inheritDoc}
     */
    public function getId()
    {
        if (!self::$sessionStarted) {
            throw new \RuntimeException('The session must be started before reading its ID');
        }

        $this->session->getSessionId();
    }

    /**
     * {@inheritDoc}
     */
    public function read($key, $default = null)
    {
        return $this->session->getDataSetDefault($key, $default);
    }

    /**
     * {@inheritDoc}
     */
    public function remove($key)
    {
        $retval = $this->session->getDataSetDefault($key, null);

        $this->session->unsetData($key);

        return $retval;
    }

    /**
     * {@inheritDoc}
     */
    public function write($key, $data)
    {
        $this->session->setData($key, $data);
    }

    /**
     * {@inheritDoc}
     */
    public function regenerate($destroy = false)
    {
        if (self::$sessionIdRegenerated) {
            return;
        }

        $this->session->regenerateSessionId();

        self::$sessionIdRegenerated = true;
    }
}
