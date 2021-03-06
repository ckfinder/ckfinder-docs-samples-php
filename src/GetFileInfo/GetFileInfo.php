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

namespace CKSource\CKFinder\Plugin\GetFileInfo;

use CKSource\CKFinder\Acl\Permission;
use CKSource\CKFinder\CKFinder;
use CKSource\CKFinder\Command\CommandAbstract;
use CKSource\CKFinder\Error;
use CKSource\CKFinder\Filesystem\Folder\WorkingFolder;
use CKSource\CKFinder\Filesystem\Path;
use CKSource\CKFinder\Plugin\PluginInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * GetFileInfo command plugin class.
 */
class GetFileInfo extends CommandAbstract implements PluginInterface
{
    /**
     * @var CKFinder
     */
    protected $app;

    /**
     * An array of permissions required by this command.
     *
     * @var array
     */
    protected $requires = [
        Permission::FILE_VIEW
    ];

    /**
     * Returns an array with default configuration for this plugin. Any of
     * the plugin configuration options can be overwritten in the CKFinder configuration file.
     *
     * @return array Default plugin configuration
     */
    public function getDefaultConfig()
    {
        return [];
    }

    /**
     * Main command method.
     *
     * @param Request       $request       Current request object
     * @param WorkingFolder $workingFolder Current working folder object
     *
     * @return array
     *
     * @throws \Exception
     */
    public function execute(Request $request, WorkingFolder $workingFolder)
    {
        $fileName = $request->get('fileName');
        $backend = $workingFolder->getBackend();

        if (!$workingFolder->containsFile($fileName)) {
            throw new \Exception('File not found', Error::FILE_NOT_FOUND);
        }

        $fileMetadada = $backend->getMetadata(Path::combine($workingFolder->getPath(), $fileName));

        return $fileMetadada;
    }
}
