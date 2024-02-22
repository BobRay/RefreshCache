<?php
/**
 * Resolver for RefreshCache extra
 *
 * Copyright 2011-2021 Bob Ray <https://bobsguides.com>
 * Created on 12-21-2018
 *
 * RefreshCache is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * RefreshCache is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * RefreshCache; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA
 * @package refreshcache
 * @subpackage build
 */

/* @var $object xPDOObject */
/* @var $modx modX */

/* @var array $options */


/* @var modTransportPackage $transport */

if ($transport) {
    $modx =& $transport->xpdo;
} else {
    $modx =& $object->xpdo;
}

 /* Make it run in either MODX 2 or MODX 3 */
 $prefix = $modx->getVersionData()['version'] >= 3
   ? 'MODX\Revolution\\'
   : '';

/* Remove obsolete files and directories */
switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:
    case xPDOTransport::ACTION_UPGRADE:
       $action = $modx->getObject($prefix . 'modAction', array('namespace' => 'refreshcache'));
       if ($action) {
           $action->remove();
       }

       $snippet = $modx->getObject($prefix . 'modSnippet', array('name' => 'RefreshCache'));
       if ($snippet) {
           $snippet->remove();
       }
       $corePath = MODX_CORE_PATH . 'components/refreshcache/';
       $assetsPath = MODX_ASSETS_PATH . 'components/refreshcache/';
       $files = array(
           $assetsPath . 'css/meter-outline.png',
           $assetsPath . 'css/refreshcache.css.old',
           $assetsPath . 'bar.css',
           $assetsPath . 'jquery.js',
           $assetsPath . 'meter-outline.png',
           $assetsPath . 'refreshcache.log',
           $assetsPath . 'refreshcache.png',
           $corePath . 'elements/snippets/refreshcache.snippet.php',
           $corePath . 'model/refreshcache/install.class.php',
           $corePath . 'class.install.php',
           $corePath . 'refreshcache.snippet.php',
           $corePath . 'index.php',
       );

       foreach($files as $file) {
           if (file_exists($file)) {
               unlink($file);
           }
       }

       $dirs = array(
           $corePath . 'elements/snippets',
           $corePath . 'elements',
       );

       foreach($dirs as $dir) {
           if (file_exists($dir)) {
               @rrmdir($dir);
           }
       }

       $settings = array(
           'refreshcache_curl_delay',
           'refreshcache_ajax_delay',
       );

       foreach ($settings as $key) {
           $setting = $modx->getObject($prefix . 'modSystemSetting', array( 'key' => $key));
           if ($setting) {
               $setting->remove();
           }
       }

       break;

    case xPDOTransport::ACTION_UNINSTALL:
        break;
}

/* Recursive function to remove non-empty directories */
function rrmdir($dir) {
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (filetype($dir . "/" . $object) == "dir") {
                    rrmdir($dir . "/" . $object);
                } else {
                    unlink($dir . "/" . $object);
                }
            }
        }
        rmdir($dir);
    }
}


return true;
