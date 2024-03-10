<?php
/**
 * resources transport file for RefreshCache extra
 *
 * Copyright 2011-2024 Bob Ray <https://bobsguides.com>
 * Created on 03-09-2024
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
/* @var xPDOObject[] $resources */


$resources = array();

$resources[1] = $modx->newObject('modResource');
$resources[1]->fromArray(array (
  'id' => 1,
  'type' => 'document',
  'contentType' => 'text/html',
  'pagetitle' => 'RefreshCache',
  'longtitle' => 'Refresh Cache',
  'description' => 'Refreshes the MODX cache',
  'alias' => 'refresh-cache',
  'alias_visible' => true,
  'link_attributes' => '',
  'published' => true,
  'isfolder' => false,
  'introtext' => 'View this page to refresh the MODX cache',
  'richtext' => false,
  'template' => 'default',
  'menuindex' => 6,
  'searchable' => true,
  'cacheable' => false,
  'createdby' => 1,
  'editedby' => 1,
  'deleted' => false,
  'deletedon' => 0,
  'deletedby' => 0,
  'menutitle' => 'Refresh Cache',
  'donthit' => false,
  'privateweb' => false,
  'privatemgr' => false,
  'content_dispo' => 0,
  'hidemenu' => false,
  'context_key' => 'web',
  'content_type' => 1,
  'hide_children_in_tree' => 0,
  'show_in_tree' => 1,
  'properties' => NULL,
), '', true, true);
$resources[1]->setContent(file_get_contents($sources['data'].'resources/refreshcache.content.html'));

return $resources;
