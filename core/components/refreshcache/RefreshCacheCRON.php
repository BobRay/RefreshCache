<?php
/**
* modified by Snow Creative for CRON use
* install this file OUTSIDE the website root for maximum security
*
*
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
*
* delay (optional) int - seconds to delay between requests; default: 1;
*/

$debug = true;  /* Turn this to see output during tests when running in a snippet or
                 from the command line. */
$cacheMin = 350;  // minimum number of files that should be in the cache, set to 0 to always refresh cache
$includeHidemenu = 0; // whether to include resources that are hidden from the menus (1 = yes, 0 = no)
$sendEmail = false;
$logCacheRefreshToErrorLog = false;
$delay = 0; // delay in seconds between fetching the next resource, if needed to reduce server load
$emailAddress = 'you@yoursite.com';

@include dirname(__FILE__, 7) . '/config.core.php';
if (!defined('MODX_CORE_PATH')) {
    @include dirname(__FILE__, 4) . '/config.core.php';
}

if (!defined('MODX_CORE_PATH')) {
    echo "\nCould not find config.core.php file";
    exit;
}

require_once MODX_CORE_PATH . 'model/modx/modx.class.php';
$modx = new modX();
if (!$modx) {
    if($debug) {
        echo "\nCould not create MODX class";
    }
    exit;
}
$modx->initialize('web');
$limit = 0;
$delay = $modx->getOption('refreshcache_request_delay',
    null, 0, true);

$siteName = $modx->getOption('site_name', null);

$prefix = $modx->getVersionData()['version'] >= 3
    ? 'MODX\Revolution\\'
    : '';

$numFiles = 0;
$cachePath = MODX_CORE_PATH . 'cache/resource/web/resources/';
if(is_dir($cachePath)) {
  $numFiles = count(glob($cachePath . "*"));
}

if($numFiles < $cacheMin) { // need to refresh the cache

  /* Set log stuff */

  $mtime = microtime();
  $mtime = explode(" ", $mtime);
  $mtime = $mtime[1] + $mtime[0];
  $tstart = $mtime;

  if (file_exists(MODX_ASSETS_PATH . 'mycomponents/refreshcache/core/components/refreshcache/processors/')) {
      $processorPath = MODX_ASSETS_PATH . 'mycomponents/refreshcache/core/components/refreshcache/processors/';
  } else {
      $processorPath = MODX_CORE_PATH . 'components/refreshcache/processors/';
  }

  $options = array(
    'processors_path' => $processorPath,
  );
  $processorReturn = $modx->runProcessor('getlist',array(), $options);

    if ($processorReturn->isError()) {
        return $processorReturn->getMessage();
    }
    /** @var $processorReturn modProcessorResponse */
    $result = $processorReturn->response;
    $result = $modx->fromJSON($result);
    $resources = $result['results'];
    $x = 1;

  if (empty($resources)) {
    $modx->log(modX::LOG_LEVEL_ERROR, '[RefreshCache] No Cacheable Resources found');
  }

  /*$ch = curl_init(); // Initialize Curl
  if ($ch === false) {
      if ($debug) {
          echo "Failed to initialize cURL\n";
      }
  }*/

 /* @curl_setopt($ch, CURLOPT_NOBODY, true);
  @curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
  @curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
  @curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);*/ // don't use a cached version of the url

  set_time_limit(0);                   // ignore php timeout
  ignore_user_abort(true);             // keep on going even if user pulls the plug



     /* while (ob_get_level()) {
          ob_end_clean();
      } // remove output buffers
      ob_implicit_flush(true);  */           // output stuff directly

      if ($debug) {
          echo "\nRefreshing " . count($resources) .
              " resources\n****************************";
      }

  /* convince the browser we mean business */
  if($debug) echo str_pad('',4096);

  $i = 1;

  foreach ($resources as $resource) {

    //  $modx->cacheManager->refresh(array($resource->getCacheKey()));

    $pageId = $resource['id'];
    $pagetitle = $resource['pagetitle'];
   // $url = $resource['uri'];
    $context = $resource['context_key'];

    if($debug) {
        echo "\n-- Refreshing: " .  $pagetitle . ' (' . $pageId . ')';
    }
      $props = array(
          'context' => $context,
          'id' => $pageId,
      );

      $processorReturn = $modx->runProcessor('refresh', $props, $options);
      usleep($delay);


    /* curl_setopt($ch, CURLOPT_URL, $url); // Set CURL options

    $output = curl_exec($ch); // get the page

    if (curl_errno($ch)) {
        $modx->log(modX::LOG_LEVEL_ERROR, "[RefreshCache] cURL error: " . curl_errno($ch) . " - " . curl_error($ch));
        if($debug) {
            echo "\ncURL error: " . curl_errno($ch) . " - " . curl_error($ch);
      }
    } */
  } /* end foreach($resources) loop */

//  curl_close($ch);

  unset($output, $ch, $pagetitle, $url, $pageId);

  $mtime = microtime();
  $mtime = explode(" ", $mtime);
  $mtime = $mtime[1] + $mtime[0];
  $tend = $mtime;
  $totalTime = ($tend - $tstart);
  $totalTime = sprintf("%2.2f s", $totalTime);

  if($debug) echo "\nFINISHED -- Execution time: {$totalTime}";
  /* keep MODX happy (MODX wants to close an output buffer - make sure there is one) */
  ob_start();

  if ($sendEmail || $logCacheRefreshToErrorLog) {
      $message = "The " . $siteName . " cache was refreshed.\n  Number of pages: " . count($resources) . "\n   Execution time: {$totalTime}";
      $subject = $siteName . ' site cache refresh';
  }
  if($sendEmail) { // send somebody a notice that the cache was refreshed
    mail($emailAddress, $subject, $message);
  }

  if ($logCacheRefreshToErrorLog) {
      $modx->log(modX::LOG_LEVEL_ERROR, $message);
  }
}
