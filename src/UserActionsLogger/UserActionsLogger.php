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

namespace CKSource\CKFinder\Plugin\UserActionsLogger;

use CKSource\CKFinder\Backend\Adapter\Local as LocalAdapter;
use CKSource\CKFinder\CKFinder;
use CKSource\CKFinder\Error;
use CKSource\CKFinder\Event\CKFinderEvent;
use CKSource\CKFinder\Filesystem\Path;
use CKSource\CKFinder\Plugin\PluginInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * UserActionsLogger plugin sample class.
 */
class UserActionsLogger implements PluginInterface, EventSubscriberInterface
{
    /**
     * @var CKFinder
     */
    protected $app;

    /**
     * Method used to inject the DI container to the plugin.
     *
     * @param CKFinder $app
     */
    public function setContainer(CKFinder $app)
    {
        $this->app = $app;
    }

    /**
     * Returns an array with the default configuration for this plugin. Any of
     * the plugin configuration options can be overwritten in the CKFinder configuration file.
     *
     * @return array Default plugin configuration
     */
    public function getDefaultConfig()
    {
        return [
            'logFilePath' => Path::combine(__DIR__, 'user_actions.log')
        ];
    }

    /**
     * Returns a file or directory path used for given event.
     *
     * @param CKFinderEvent $event     Event object
     * @param string        $eventName Event name
     *
     * @return string file or directory path - depending on event type
     */
    protected function getPathFromEvent(CKFinderEvent $event, $eventName)
    {
        $workingFolder = $event->getContainer()->getWorkingFolder();
        $path = '';

        switch ($eventName) {
            case CKFinderEvent::COPY_FILE:
                /* @var $event \CKSource\CKFinder\Event\CopyFileEvent */
                $path = $event->getCopiedFile()->getFilePath();
                break;
            case CKFinderEvent::DELETE_FILE:
                /* @var $event \CKSource\CKFinder\Event\DeleteFileEvent */
                $path = $event->getDeletedFile()->getFilePath();
                break;
            case CKFinderEvent::DOWNLOAD_FILE:
                /* @var $event \CKSource\CKFinder\Event\DownloadFileEvent */
                $path = $event->getDownloadedFile()->getFilePath();
                break;
            case CKFinderEvent::MOVE_FILE:
                /* @var $event \CKSource\CKFinder\Event\MoveFileEvent */
                $path = $event->getMovedFile()->getFilePath();
                break;
            case CKFinderEvent::RENAME_FILE:
                /* @var $event \CKSource\CKFinder\Event\RenameFileEvent */
                $path = $event->getRenamedFile()->getFilePath();
                break;
            case CKFinderEvent::SAVE_IMAGE:
            case CKFinderEvent::EDIT_IMAGE:
                /* @var $event \CKSource\CKFinder\Event\EditFileEvent */
                $path = $event->getEditedFile()->getFilePath();
                break;
            case CKFinderEvent::CREATE_RESIZED_IMAGE:
                /* @var $event \CKSource\CKFinder\Event\ResizeImageEvent */
                $path = $event->getResizedImage()->getFilePath();
                break;
            case CKFinderEvent::CREATE_FOLDER:
                /* @var $event \CKSource\CKFinder\Event\CreateFolderEvent */
                $path = Path::combine($workingFolder->getPath(), $event->getNewFolderName());
                break;
            case CKFinderEvent::DELETE_FOLDER:
            case CKFinderEvent::RENAME_FOLDER:
                $path = Path::combine($workingFolder->getPath());
                break;
            default:
                return 'undefined';
        }

        $backend = $workingFolder->getBackend();
        $adapter = $backend->getAdapter();

        return $adapter instanceof LocalAdapter ? $adapter->applyPathPrefix($path) : $path;
    }

    /**
     * Event listener method that logs user actions.
     *
     * @param CKFinderEvent $event     Event object
     * @param string        $eventName Event name
     *
     * @throws \Exception if the log file is not writable.
     */
    public function logUserAction(CKFinderEvent $event, $eventName)
    {
        global $user; // Global dummy user object

        $logLine = sprintf("[%s] - %s : %s (used path: %s)\n", date('Y.m.d H:i:s'), $user->getUsername(), $eventName, $this->getPathFromEvent($event, $eventName));

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
     * @return array The event names to listen to
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
            CKFinderEvent::CREATE_RESIZED_IMAGE
        ];

        return array_fill_keys($actionsToListen, 'logUserAction');
    }
}
