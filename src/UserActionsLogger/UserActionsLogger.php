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
use CKSource\CKFinder\ResourceType\ResourceType;
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
     * Returns a more detailed information about logged operation.
     *
     * Note: Due to the fact that all paths used by CKFinder needs to be relative, all paths in the log use following format:
     * [backend name]://backend/relative/path
     *
     * @param CKFinderEvent $event     Event object
     * @param string        $eventName Event name
     *
     * @return string more detailed information about the event - depending on event type
     */
    protected function getInfoFromEvent(CKFinderEvent $event, $eventName)
    {
        $workingFolder = $event->getContainer()->getWorkingFolder();

        switch ($eventName) {
            case CKFinderEvent::MOVE_FILE:
            case CKFinderEvent::COPY_FILE:
                /* @var $event \CKSource\CKFinder\Event\CopyFileEvent */
                $copiedFile = $event->getFile();
                $sourcePath = $this->createPath($copiedFile->getResourceType(), $copiedFile->getSourceFilePath());
                $targetPath = $this->createPath($copiedFile->getTargetFolder()->getResourceType(), $copiedFile->getTargetFilePath());
                return $sourcePath . ' -> ' . $targetPath;
            case CKFinderEvent::DELETE_FILE:
            case CKFinderEvent::DOWNLOAD_FILE:
            case CKFinderEvent::SAVE_IMAGE:
            case CKFinderEvent::EDIT_IMAGE:
                /* @var $file \CKSource\CKFinder\Filesystem\File\ExistingFile */
                $file = $event->getFile();
                return $this->createPath($file->getResourceType(), $file->getFilePath());
            case CKFinderEvent::RENAME_FILE:
                /* @var $event \CKSource\CKFinder\Event\RenameFileEvent */
                $renamedFile = $event->getFile();
                $resourceType = $renamedFile->getResourceType();
                $sourcePath = $this->createPath($resourceType, $renamedFile->getFilePath());
                $targetPath = $this->createPath($resourceType, $renamedFile->getNewFilePath());
                return $sourcePath . ' -> ' . $targetPath;
            case CKFinderEvent::CREATE_RESIZED_IMAGE:
                /* @var $event \CKSource\CKFinder\Event\ResizeImageEvent */
                $resizedImage = $event->getResizedImage();
                return $this->createPath($resizedImage->getResourceType(), $resizedImage->getFilePath());
            case CKFinderEvent::CREATE_FOLDER:
                /* @var $event \CKSource\CKFinder\Event\CreateFolderEvent */
                return $this->createPath($workingFolder->getResourceType(), Path::combine($workingFolder->getPath(), $event->getNewFolderName()));
            case CKFinderEvent::DELETE_FOLDER:
                return $this->createPath($workingFolder->getResourceType(), $workingFolder->getPath());
            case CKFinderEvent::RENAME_FOLDER:
                /* @var $event \CKSource\CKFinder\Event\RenameFolderEvent */
                $resourceType = $workingFolder->getResourceType();
                return $this->createPath($resourceType, $workingFolder->getPath()) . ' -> ' . $this->createPath($resourceType, Path::combine(dirname($workingFolder->getPath()), $event->getNewFolderName()));
        }
    }

    /**
     * Creates a path in format:
     * [backend name]://backend/relative/path
     *
     * @param ResourceType $resourceType resource type
     * @param string       $path         backend relative path

     * @return string formatted path
     */
    protected function createPath(ResourceType $resourceType, $path)
    {
        return $resourceType->getBackend()->getName() . '://' . $path;
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

        $logLine = sprintf("[%s] - %s : %s (%s)\n", date('Y.m.d H:i:s'), $user->getUsername(), $eventName, $this->getInfoFromEvent($event, $eventName));

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
            CKFinderEvent::CREATE_RESIZED_IMAGE
        ];

        return array_fill_keys($actionsToListen, 'logUserAction');
    }
}
