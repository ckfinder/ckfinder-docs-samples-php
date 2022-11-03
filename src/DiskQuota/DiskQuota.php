<?php

/*
 * CKFinder
 * ========
 * https://ckeditor.com/ckfinder/
 * Copyright (c) 2007-2021, CKSource - Frederico Knabben. All rights reserved.
 *
 * The software, this file and its contents are subject to the CKFinder
 * License. Please read the license.txt file before using, installing, copying,
 * modifying or distribute this file or part of its contents. The contents of
 * this file is part of the Source Code of CKFinder.
 */

namespace CKSource\CKFinder\Plugin\DiskQuota;

use CKSource\CKFinder\CKFinder;
use CKSource\CKFinder\Error;
use CKSource\CKFinder\Event\CKFinderEvent;
use CKSource\CKFinder\Plugin\PluginInterface;
use CKSource\CKFinder\Utils;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * DiskQuota plugin sample class.
 */
class DiskQuota implements PluginInterface, EventSubscriberInterface
{
    /**
     * @var CKFinder
     */
    protected $app;

    /**
     * Method used to inject the DI container to the plugin.
     */
    public function setContainer(CKFinder $app)
    {
        $this->app = $app;
    }

    /**
     * Returns an array with default configuration for this plugin. Any of
     * the plugin configuration options can be overwritten in the CKFinder configuration file.
     *
     * @return array Default plugin configuration
     */
    public function getDefaultConfig()
    {
        return [
            'userQuota' => '100MB', // Quota defined using PHP shorthand byte value
        ];                         // (http://php.net/manual/pl/faq.using.php#faq.using.shorthandbytes)
    }

    /**
     * Event listener checking current user quota.
     *
     * @throws \Exception if storage quota for the current user is exceeded
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
     *  * The method name to call (priority defaults to 0).
     *  * An array composed of the method name to call and the priority.
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset.
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array the event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            CKFinderEvent::BEFORE_COMMAND_FILE_UPLOAD => 'checkQuota',
            CKFinderEvent::BEFORE_COMMAND_COPY_FILES => 'checkQuota',
            CKFinderEvent::BEFORE_COMMAND_IMAGE_RESIZE => 'checkQuota',
            CKFinderEvent::BEFORE_COMMAND_CREATE_FOLDER => 'checkQuota',
        ];
    }

    /**
     * Checks if the current user has any storage quota left.
     *
     * @return bool `false` if current user storage quota was exceeded, `true` otherwise
     */
    protected function isQuotaAvailable()
    {
        // Get the user quota in bytes.
        $quota = Utils::returnBytes($this->app['config']->get('DiskQuota.userQuota'));

        /*
         * For documentation purposes it is only a method stub.
         *
         * @todo Custom implementation of the current user quota check.
         */

        return true;
    }
}
