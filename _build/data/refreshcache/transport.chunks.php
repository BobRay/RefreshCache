<?php
/**
 * chunks transport file for RefreshCache extra
 *
 * Copyright 2011-2013 by Bob Ray <http://bobsguides.com>
 * Created on 07-15-2013
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
/* @var xPDOObject[] $chunks */


$chunks = array();

$chunks[1] = $modx->newObject('modChunk');
$chunks[1]->fromArray(array (
  'id' => 1,
  'property_preprocess' => false,
  'name' => 'ProgressBarJs',
  'description' => 'JavaScript for Progress Bar',
  'properties' => NULL,
), '', true, true);
$chunks[1]->setContent(file_get_contents($sources['source_core'] . '/elements/chunks/progressbar.js'));

$chunks[2] = $modx->newObject('modChunk');
$chunks[2]->fromArray(array (
  'id' => 2,
  'property_preprocess' => false,
  'name' => 'PollingJs',
  'description' => 'JavaScript for Ajax polling',
  'properties' => NULL,
), '', true, true);
$chunks[2]->setContent(file_get_contents($sources['source_core'] . '/elements/chunks/pollingjs.js'));

return $chunks;
