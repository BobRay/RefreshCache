<?php
/**
 * systemSettings transport file for RefreshCache extra
 *
 * Copyright 2011-2024 Bob Ray <https://bobsguides.com>
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
/* @var xPDOObject[] $systemSettings */


$systemSettings = array();

$systemSettings[1] = $modx->newObject('modSystemSetting');
$systemSettings[1]->fromArray(array (
  'key' => 'refreshcache_request_delay',
  'value' => '0',
  'xtype' => 'textfield',
  'namespace' => 'refreshcache',
  'area' => 'RefreshCache',
  'name' => 'Refresh Cache Request Delay',
  'description' => 'Delay between cURL requests (in seconds); note that there is a built-in delay of about 1 second; default: 0',
), '', true, true);
$systemSettings[2] = $modx->newObject('modSystemSetting');
$systemSettings[2]->fromArray(array (
  'key' => 'refreshcache_log_all_errors',
  'value' => '0',
  'xtype' => 'combo-boolean',
  'namespace' => 'refreshcache',
  'area' => 'RefreshCache',
  'name' => 'Refresh Cache Log All Errors',
  'description' => 'Log all errors to MODX Error Log; default: no',
), '', true, true);
$systemSettings[3] = $modx->newObject('modSystemSetting');
$systemSettings[3]->fromArray(array (
  'key' => 'refreshcache_honor_hidemenu',
  'value' => '1',
  'xtype' => 'combo-boolean',
  'namespace' => 'refreshcache',
  'area' => 'RefreshCache',
  'name' => 'Refresh Cache Honor hidemenu',
  'description' => 'Do not refresh resources hidden from menus; default; yes',
), '', true, true);
$systemSettings[4] = $modx->newObject('modSystemSetting');
$systemSettings[4]->fromArray(array (
  'key' => 'refreshcache_limit',
  'value' => '0',
  'xtype' => 'numberfield',
  'namespace' => 'refreshcache',
  'area' => 'RefreshCache',
  'name' => 'Refresh Cache Limit',
  'description' => 'Maximum number of resources for getList to retrieve; default: 0 (no limit)',
), '', true, true);
return $systemSettings;
