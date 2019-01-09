<?php
/**
 * systemSettings transport file for RefreshCache extra
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
/* @var xPDOObject[] $systemSettings */


$systemSettings = array();

$systemSettings[1] = $modx->newObject('modSystemSetting');
$systemSettings[1]->fromArray(array (
  'key' => 'refreshcache_request_delay',
  'value' => 0,
  'xtype' => 'textfield',
  'namespace' => 'refreshcache',
  'area' => 'RefreshCache',
  'name' => 'RefreshCache Request delay',
  'description' => 'Delay between page requests (in milliseconds -- 1000 = 1 second); default: 0',
), '', true, true);
$systemSettings[2] = $modx->newObject('modSystemSetting');
$systemSettings[2]->fromArray(array (
  'key' => 'refreshcache_log_all_errors',
  'value' => '0',
  'xtype' => 'combo-boolean',
  'namespace' => 'refreshcache',
  'area' => 'RefreshCache',
  'name' => 'RefreshCache Log All Errors',
  'description' => 'Log all access errors in the MODX error log when refreshing the cache; default: no.',
), '', true, true);
return $systemSettings;
