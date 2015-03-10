<?php

/*
 * CKFinder
 * ========
 * http://cksource.com/ckfinder
 * Copyright (C) 2007-2015, CKSource - Frederico Knabben. All rights reserved.
 *
 * The software, this file and its contents are subject to the MIT License.
 * Please read the LICENSE.md file before using, installing, copying,
 * modifying or distribute this file or part of its contents.
 */

namespace CKSource\CKFinder\Plugin\DiskQuota;

use CKSource\CKFinder\CKFinder;
use CKSource\CKFinder\Error;
use CKSource\CKFinder\Event\CKFinderEvent;
use CKSource\CKFinder\Plugin\PluginInterface;
use CKSource\CKFinder\Utils;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * DiskQuota plugin sample class
 */
class DiskQuota implements PluginInterface, EventSubscriberInterface
{
    /**
     * @var CKFinder
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
            'userQuota' => '100MB' // Quota defined using PHP shorthand byte value
        ];                         // (http://php.net/manual/pl/faq.using.php#faq.using.shorthandbytes)

    }

    /**
     * Checks if current user has any storage quota left.
     *
     * @return bool false if current user storage quota has been exceeded, true otherwise
     */
    protected function isQuotaAvailable()
    {
        // Get user quota in bytes
        $quota = Utils::returnBytes($this->app['config']->get('DiskQuota.userQuota'));

        /**
         * For documentation purposes it's only a method stub.
         *
         * @todo custom implementation of current user quota check
         */

        return true;
    }

    /**
     * Event listener checking current user quota
     *
     * @throws \Exception if storage quota for current user exceeded
     */
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
