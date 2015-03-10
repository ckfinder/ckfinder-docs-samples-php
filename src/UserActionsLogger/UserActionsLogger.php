<?php

/*
 * CKFinder
 * ========
 * http://cksource.com/ckfinder
 * Copyright (C) 2007-2015, CKSource - Frederico Knabben. All rights reserved.
 *
 * The software, this file and its contents are subject to the CKFinder
 * License. Please read the license.txt file before using, installing, copying,
 * modifying or distribute this file or part of its contents. The contents of
 * this file is part of the Source Code of CKFinder.
 */

namespace CKSource\CKFinder\Plugin\UserActionsLogger;

use CKSource\CKFinder\CKFinder;
use CKSource\CKFinder\Error;
use CKSource\CKFinder\Event\CKFinderEvent;
use CKSource\CKFinder\Filesystem\Path;
use CKSource\CKFinder\Plugin\PluginInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * UserActionsLogger plugin sample class
 */
class UserActionsLogger implements PluginInterface, EventSubscriberInterface
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
            'logFilePath' => Path::combine(__DIR__, 'user_actions.log')
        ];
    }

    /**
     * Event listener method that logs user actions
     *
     * @param CKFinderEvent $event     event object
     * @param string        $eventName event name
     *
     * @throws \Exception if log file is not writable
     */
    public function logUserAction(CKFinderEvent $event, $eventName)
    {
        global $user; // Global dummy user object

        $logLine = sprintf("[%s] - %s : %s\n", date('Y.m.d H:i:s'), $user->getUsername(), $eventName);

        $logFilePath = $this->app['config']->get('UserActionsLogger.logFilePath');

        if (!is_writable($logFilePath)) {
            throw new \Exception('UserActionsLogger: the log file is not writable', Error::CUSTOM_ERROR);
        }

        file_put_contents($logFilePath, $logLine, FILE_APPEND);
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
        $actionsToListen = [
            CKFinderEvent::COPY_FILE,
            CKFinderEvent::CREATE_FOLDER,
            CKFinderEvent::DELETE_FILE,
            CKFinderEvent::DELETE_FOLDER,
            CKFinderEvent::DOWNLOAD_FILE,
            CKFinderEvent::FILE_UPLOAD,
            CKFinderEvent::MOVE_FILE,
            CKFinderEvent::RENAME_FILE,
            CKFinderEvent::RENAME_FOLDER,
            CKFinderEvent::SAVE_IMAGE,
            CKFinderEvent::EDIT_IMAGE,
            CKFinderEvent::CREATE_THUMBNAIL,
            CKFinderEvent::CREATE_SCALED_IMAGE
        ];

        return array_fill_keys($actionsToListen, 'logUserAction');
    }
}
