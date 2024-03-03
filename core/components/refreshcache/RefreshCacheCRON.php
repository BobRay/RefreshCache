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
 * Description: refreshes the cache using calls to MODX core methods.
 * Refreshes all site pages that are
 * published, undeleted, cacheable, and not hidden from menus.
 *
 * The point is for this program to spend time waiting for the pages so
 * the site visitors don't have to. They'll see cached pages, which will be
 * delivered much faster.
 *
 *
 * The larger the site, the longer it takes. If you run bot-blocking software,
 * you'll want to set the RefreshCache Request Delay setting to at least 1
 * (one second), to prevent triggering the software. You may also set it to
 * fractions of a second, e.g., 0.5
 *
 * With a delay of 0, at Bob's Guides, it will refresh about 5 pages per second.
 * Complex pages with a lot of tags to process, many conditional output
 * filters, or long-running snippet can take longer.
 *
 * See the variables below to change options.
 *
 * To install, paste the code into a snippet called refresh-cache. Create a resource
 * called Refresh Cache with just the snippet tag: [[!ClearCache]].
 *
 * Important!: The resource should not be cacheable and the alias should be
 * refresh-cache to prevent an infinite loop and to prevent the cache-clear
 * snippet (if you have it installed) from undoing its work.
 *
 * Create an empty template with just this tag: [[*content]] and be sure to
 * assign that template to the resource.
 *
 * To test the snippet, preview the Refresh Cache resource.
 *
 * The snippet can be further configured by setting the System Settings in the
 * refreshcache namespace.
 *
 * @package refreshCache
 *
 *
 * delay (optional) int - seconds to delay between requests; default: 0;
 */

$debug = true;  /* Turn this on to see output during tests when running in a snippet or from the command line. */

$cacheMin = 350;  // minimum number of files that should be in the cache, set to 0 to always refresh cache

$includeHidemenu = 0; // whether to include resources that are hidden from the menus (1 = yes, 0 = no)

/* Attempt to send email for each run */
$sendEmail = false;
$emailAddress = 'you@yoursite.com';

/* Log final results to the MODX Error Log */
$logResults = false;

/* For dev env. */
@include dirname(__FILE__, 7) . '/config.core.php';

/* For production env. */
if (!defined('MODX_CORE_PATH')) {
    @include dirname(__FILE__, 4) . '/config.core.php';
}

if (!defined('MODX_CORE_PATH')) {
    echo "\nCould not find config.core.php file";
    exit;
}

/* Path to where your cached resources are. Used only to
   help calculation for cacheMin. Does not affect cache
   file location */
$cachePath = MODX_CORE_PATH . 'cache/resource/web/resources/';

require_once MODX_CORE_PATH . 'model/modx/modx.class.php';
$modx = new modX();
if (!$modx) {
    if ($debug) {
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

if (is_dir($cachePath)) {
    $numFiles = count(glob($cachePath . "*"));
}

if ($numFiles < $cacheMin) { // need to refresh the cache

    /* Set log stuff */

    $mtime = microtime();
    $mtime = explode(" ", $mtime);
    $mtime = (float)$mtime[1] + (float)$mtime[0];
    $tstart = $mtime;

    if (file_exists(MODX_ASSETS_PATH . 'mycomponents/refreshcache/core/components/refreshcache/processors/')) {
        /* Development environment */
        $processorPath = MODX_ASSETS_PATH . 'mycomponents/refreshcache/core/components/refreshcache/processors/';
    } else {
        /* Production env. */
        $processorPath = MODX_CORE_PATH . 'components/refreshcache/processors/';
    }

    $options = array(
        'processors_path' => $processorPath,
    );
    $processorReturn = $modx->runProcessor('getlist', array(), $options);

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

    if ($debug) {
        echo "\nRefreshing " . count($resources) .
            " resources\n****************************";
    }

    /* convince the browser we mean business */
    if ($debug) {
        echo str_pad('', 4096);
    }

    $i = 1;

    /* Resources are not objects here, just arrays of fields
       selected in GetList processor */
    foreach ($resources as $resource) {
        $pageId = $resource['id'];
        $pagetitle = $resource['pagetitle'];

        $context = $resource['context_key'];

        if ($debug) {
            echo "\n-- Refreshing: " . $pagetitle . ' (' . $pageId . ')';
        }
        $props = array(
            'context' => $context,
            'id' => $pageId,
        );

        $processorReturn = $modx->runProcessor('refresh', $props, $options);
        usleep($delay * 1000);
    } /* end foreach($resources) loop */

    unset($output, $ch, $pagetitle, $url, $pageId);

    $mtime = microtime();
    $mtime = explode(" ", $mtime);
    $mtime = (float)$mtime[1] + (float)$mtime[0];
    $tend = $mtime;
    $totalTime = ($tend - $tstart);
    $totalTime = sprintf("%2.2f s", $totalTime);

    if ($debug) {
        echo "\nFINISHED -- Execution time: {$totalTime}";
    }
    /* keep MODX happy (MODX wants to close an output buffer - make sure there is one) */
    ob_start();

    if ($sendEmail || $logResults) {
        $message = "The " . $siteName . " cache was refreshed.\n  Number of pages: " . count($resources) . "\n   Execution time: {$totalTime}";
        $subject = $siteName . ' site cache refresh';
    }
    if ($sendEmail) { // send somebody a notice that the cache was refreshed
        mail($emailAddress, $subject, $message);
    }

    if ($logResults) {
        $modx->log(modX::LOG_LEVEL_ERROR, $message);
    }
}
