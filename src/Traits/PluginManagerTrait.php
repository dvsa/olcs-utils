<?php

namespace Dvsa\Olcs\Utils\Traits;

use Laminas\ServiceManager\Exception\InvalidServiceException;
use Laminas\ServiceManager\Exception\RuntimeException;

/**
 * PluginManagerTrait
 */
trait PluginManagerTrait
{
    /**
     * {@inheritDoc}
     *
     * This method mimics how the validate() method is implemented by laminas-servicemanager 3.x
     * https://github.com/laminas/laminas-servicemanager/blob/3.0.0/src/AbstractPluginManager.php#L150
     *
     * @todo To be removed as part of OLCS-28149
     */
    public function validate($instance)
    {
        if (empty($this->instanceOf) || $instance instanceof $this->instanceOf) {
            return;
        }

        throw new InvalidServiceException(sprintf(
            'Plugin manager "%s" expected an instance of type "%s", but "%s" was received',
            __CLASS__,
            $this->instanceOf,
            is_object($instance) ? get_class($instance) : gettype($instance)
        ));
    }

    /**
     * {@inheritDoc}
     *
     * This method is required to validate plugin for laminas-servicemanager 2.x
     * https://github.com/laminas/laminas-servicemanager/blob/2.7.11/src/AbstractPluginManager.php#L128
     *
     * @todo To be removed as part of OLCS-28149
     */
    public function validatePlugin($plugin)
    {
        try {
            $this->validate($plugin);
        } catch (InvalidServiceException $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
