<?php
/**
 * menus transport file for RefreshCache extra
 *
 * Copyright 2011-2018 by Bob Ray <https://bobsguides.com>
 * Created on 07-18-2013
 *
 * @package refreshcache
 * @subpackage build
 */

if (! function_exists('stripPhpTags')) {
    function stripPhpTags($filename) {
        $o = file_get_contents($filename);
        $o = str_replace('<' . '?' . 'php', '', $o);
        $o = str_replace('?>', '', $o);
        $o = trim($o);
        return $o;
    }
}
/* @var $modx modX */
/* @var $sources array */
/* @var xPDOObject[] $menus */


$menus[1] = $modx->newObject('modMenu');
$menus[1]->fromArray( array (
  'text' => 'RefreshCache',
  'parent' => 'components',
  'action' => 'index',
  'description' => 'Refresh the MODX cache',
  'icon' => '',
  'menuindex' => 0,
  'params' => '',
  'handler' => '',
  'permissions' => '',
  'namespace' => 'refreshcache',
  'id' => 1,
), '', true, true);

return $menus;
