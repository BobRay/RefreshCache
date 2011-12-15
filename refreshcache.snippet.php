<?php
/**
 * RefreshCache
 *
 * Copyright 2011 Bob Ray
 *
 * @author Bob Ray
 * 12/13/11
 *
 * RefreshCache is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation; either version 2 of the License, or (at your option) any
 * later version.
 *
 * RefreshCache is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * RefreshCache; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package refreshCache
 */
/**
 * MODx RefreshCache Snippet
 *
 * Description: refreshes the cache by visiting all site pages that are
 * published, undeleted, cacheable, and not hidden from menus.
 *
 * The point is for this program to spend time waiting for the pages so
 * the site visitors don't have to. They'll see cached pages, which will be
 * delivered much faster.
 *
 * RefreshCache is an inelegant, brute-force snippet. It refreshes the cache by
 * requesting every page with cURL. On many servers, it will produce no
 * visible output at all until finished -- you'll be looking at a blank screen.
 *
 * The larger the site, the longer it takes. It's intentionally slow to avoid
 * stressing the server and to keep from running afoul of bot-blocking software.
 *
 * On a 100-page site, at broadband speeds, it can take 5-10 minutes to run,
 * depending on the connection speed and how complex the pages are.
 * Larger sites can take much longer.
 *
 * The only settable property is &delay, which sets the number of seconds to sleep
 * between page requests. The default is 1 second.
 *
 * To install, paste the code into a snippet called clear-cache. Create a resource
 * called ClearCache with just the snippet tag: [[!ClearCache]].
 *
 * The resource should not be cacheable and the alias should be
 * refresh-cache.
 *
 * Create an empty template with just this tag: [[*content]] and be sure to
 * assign that template to the resource.
 *
 * To run the snippet, preview the ClearCache resource.
 *
 * @package refreshCache
 *
 * @property
 *
 * delay (optional) int - seconds to delay between requests; default: 1;
 */

if (!defined('MODX_CORE_PATH')) {
    $outsideModx = true;
    /* put the path to your core in the next line to run outside of MODx */
    define(MODX_CORE_PATH, 'c:/xampp/htdocs/addons/core/');
    require_once MODX_CORE_PATH . '/model/modx/modx.class.php';
    $modx = new modX();
    if (!$modx) {
        echo 'Could not create MODX class';
    }
    $modx->initialize('web');
} else {
    $outsideModx = false;
}

/* Set log stuff */

$mtime = microtime();
$mtime = explode(" ", $mtime);
$mtime = $mtime[1] + $mtime[0];
$tstart = $mtime;
set_time_limit(0);


$delay = $modx->getOption('delay', $scriptProperties, 1);

/* Ignore resources that are uncached, deleted, unpublished, or hidden from menus */

$c = array(
  'published'=>'1',
  'deleted'=>'0',
  'cacheable'=>'1',
  'hidemenu' => '0',
  'class_key' => 'modDocument',

);
$count = $modx->getCount('modResource',$c);
$resources = $modx->getCollection('modResource', $c);

if (empty($resources)) {
    die('No Cacheable Resources found');
}
$ch = curl_init(); // Initialize Curl
if ($ch === false) {
    echo "Failed to initialize cURL\n";
}

@curl_setopt($ch, CURLOPT_NOBODY, TRUE);
@curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
@curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
@curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1); // don't use a cached version of the url

set_time_limit(0);                   // ignore php timeout
ignore_user_abort(true);             // keep on going even if user pulls the plug*
while(ob_get_level())ob_end_clean(); // remove output buffers
ob_implicit_flush(true);             // output stuff directly

echo '<b>Refreshing ' . $count . ' resources</b><br /><br />';

/* convince the browser we mean business */
echo str_pad('',4096);

$i = 1;
$endLine = $outsideModx? "\n" : '<br />';
foreach ($resources as $resource) {
    $pageId = $resource->get('id');
    $pagetitle = $resource->get('pagetitle');
    $url = $modx->makeUrl($pageId, '','', 'full');
    /* Avoid infinite loop when requesting this page */
    if (strstr($url, 'refresh-cache' || $modx->$resource->get('id') == $pageId)) {
        continue;
    }

    echo sprintf("%1$04d",$i) . ' -- Refreshing: ' .  $url . $endLine;
    $i++;

    sleep($delay);
    flush();


    curl_setopt($ch, CURLOPT_URL, $url); // Set CURL options

    $output = curl_exec($ch); // get the page

    if (curl_errno($ch)) {
        echo 'cURL error: ' . curl_errno($ch) . " - " . curl_error($ch);
    }
} /* end foreach($resources) loop */

curl_close($ch);

unset($output,$ch, $pagetitle,$url, $pageId);

$mtime= microtime();
$mtime= explode(" ", $mtime);
$mtime= $mtime[1] + $mtime[0];
$tend= $mtime;
$totalTime= ($tend - $tstart);
$totalTime= sprintf("%2.2f s", $totalTime);

echo "<br />FINISHED -- Execution time: {$totalTime}";
/* keep MODX happy (MODX wants to close an output buffer - make sure there is one) */
ob_start();
return '';
