<?php
/**
 * menus transport file for RefreshCache extra
 *
 * Copyright 2011-2013 by Bob Ray <http://bobsguides.com>
 * Created on 07-17-2013
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

$action = $modx->newObject('modAction');
$action->fromArray( array (
  'namespace' => 'refreshcache',
  'controller' => 'index',
  'haslayout' => 1,
  'lang_topics' => '',
  'assets' => '',
  'help_url' => '',
  'id' => 1,
), '', true, true);

$menus[1] = $modx->newObject('modMenu');
$menus[1]->fromArray( array (
  'text' => 'Refresh Cache',
  'parent' => 'components',
  'description' => 'rc_menu_desc~~Refresh the cache for all cacheable resources',
  'icon' => '',
  'menuindex' => 0,
  'params' => '',
  'handler' => '',
  'permissions' => '',
  'id' => 1,
), '', true, true);
$menus[1]->addOne($action);

return $menus;
