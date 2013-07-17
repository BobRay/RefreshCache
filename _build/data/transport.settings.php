<?php
/**
 * systemSettings transport file for RefreshCache extra
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
/* @var xPDOObject[] $systemSettings */


$systemSettings = array();

$systemSettings[1] = $modx->newObject('modSystemSetting');
$systemSettings[1]->fromArray(array (
  'key' => 'refreshcache_ajax_delay',
  'value' => '900',
  'xtype' => 'textfield',
  'namespace' => 'refreshcache',
  'area' => '',
  'name' => 'RefreshCache Ajax delay',
  'description' => 'Delay between JS polling checks (in milliseconds); default: 900',
), '', true, true);
$systemSettings[2] = $modx->newObject('modSystemSetting');
$systemSettings[2]->fromArray(array (
  'key' => 'refreshcache_curl_delay',
  'value' => '0',
  'xtype' => 'textfield',
  'namespace' => 'refreshcache',
  'area' => '',
  'name' => 'RefreshCache cUrl Delay',
  'description' => 'Delay between cURL requests (in seconds); default: 0',
), '', true, true);
return $systemSettings;
