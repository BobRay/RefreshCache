<?php
/**
 * Resolver for RefreshCache extra
 *
 * Copyright 2011-2013 by Bob Ray <https://bobsguides.com>
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

if ($object->xpdo) {
    $modx =& $object->xpdo;
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
           $action = $modx->getObject('modAction', array('namespace' => 'refreshcache'));
           if ($action) {
               $action->remove();
           }

           $snippet = $modx->getObject('modSnippet', array('name' => 'RefreshCache'));
           if ($snippet) {
               $snippet->remove();
           }
           $corePath = MODX_CORE_PATH . 'components/refreshcache';
           $assetsPath = MODX_ASSETS_PATH . 'components/refreshcache';
           $files = array(
               $assetsPath . 'css/meter-outline.png',
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
               $corePath . 'model/refreshcache',
               $corePath . 'model',
           );

           foreach($dirs as $dir) {
               if (file_exists($dir)) {
                   rmdir($dir);
               }
           }

           $settings = array(
               'refreshcache_curl_delay',
               'refreshcache_ajax_delay',
           );

           foreach ($settings as $key) {
               $setting = $modx->getObject('modSystemSetting', array( 'key' => $key));
               if ($setting) {
                   $setting->remove();
               }
           }

           break;

        case xPDOTransport::ACTION_UNINSTALL:
            break;
    }
}

return true;