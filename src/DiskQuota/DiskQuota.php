<?php

namespace CKSource\CKFinder\Plugin\DiskQuota;

use CKSource\CKFinder\CKFinder;
use CKSource\CKFinder\Error;
use CKSource\CKFinder\Event\CKFinderEvent;
use CKSource\CKFinder\Plugin\PluginInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DiskQuota implements PluginInterface, EventSubscriberInterface
{
    /**
     * @var CKFinder $app
     */
    protected $app;

    /**
     * Method used to inject DI container to the plugin
     *
     * @param CKFinder $app
     */
    public function setContainer(CKFinder $app)
    {
        $this->app = $app;
    }

    /**
     * Returns an array with default configuration for this plugin. Any of
     * the plugin config options can be overwritten in CKFinder configuration file.
     *
     * @return array plugin default configuration
     */
    public function getDefaultConfig()
    {
        return [
            'userQuota' => '100MB'
        ];
    }

    /**
     * Checks if current user has any storage quota left.
     *
     * @return bool false if current user storage quota has been exceeded, true otherwise
     */
    protected function isQuotaAvailable()
    {
        /**
         * For documentation purposes it's only a method stub.
         *
         * @todo needs custom implementation
         */

        return true;
    }

    public function checkQuota()
    {
        if (!$this->isQuotaAvailable()) {
            throw new \Exception('Storage quota exceeded', Error::CUSTOM_ERROR);
        }
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     *
     * @api
     */
    public static function getSubscribedEvents()
    {
        return [
            CKFinderEvent::BEFORE_COMMAND_FILE_UPLOAD   => 'checkQuota',
            CKFinderEvent::BEFORE_COMMAND_COPY_FILES    => 'checkQuota',
            CKFinderEvent::BEFORE_COMMAND_IMAGE_SCALE   => 'checkQuota',
            CKFinderEvent::BEFORE_COMMAND_CREATE_FOLDER => 'checkQuota'
        ];
    }
}
