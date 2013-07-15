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

if (! $modx->user->isMember('Administrator') ) {
    return 'This code can only be run by an administrator';
}

$modx->regClientStartupScript("http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js");

//include class
$path = $modx->getOption('refresh_cache_core_path', null, $modx->getOption('core_path') . 'components/refreshcache/') . 'model/refreshcache/';
require_once($path . 'class.install.php');

//initialize class
$install = new Installer($modx);

echo "\n" . '
<!-- Remember to add form id="apiform" and target="progressFrame" to make script work -->
<center><form id="apiform" target="progressFrame" method="post">
                    <input id="apisubmit" type="submit" name="submit" value="Refresh the Cache">
                    </form></center>';

//load form, define progress bar colours
$install->placeholder();

$delay = $modx->getOption('delay', $scriptProperties, 0.51);

$mtime = microtime();
$mtime = explode(" ", $mtime);
$mtime = $mtime[1] + $mtime[0];
$tstart = $mtime;



if (isset($_POST['submit'])) {

    /* Ignore resources that are uncached, deleted,
        or unpublished */
    set_time_limit(0);
    $query = $modx->newQuery('modResource');
    $query->limit(5);
    $query->where(array(
           array(
               'class_key:=' => 'modDocument',
               'OR:class_key:=' => 'Article',
           ),
           array(
               'AND:published:=' => '1',
               'AND:deleted:=' => '0',
               'AND:cacheable:=' => '1',
           )
    ));
    $resources = $modx->getCollection('modResource', $query);
    $count = count($resources);  //set number of process steps
    $install->setSteps($count+2);

    $install->defineBar('blue', 'navy');

    if (empty($resources)) {
        $output = 'No Cacheable Resources found';
        $install->save($output);
        $install->delay(01);
    }

    $ch = curl_init(); // Initialize Curl
    if ($ch === false) {
        $output = "Failed to initialize cURL";
        $install->save($output);
        $install->delay(3);
    }

    @curl_setopt($ch, CURLOPT_NOBODY, TRUE);
    @curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    @curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
    @curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1); // don't use a cached version of the url

    ignore_user_abort(true); // keep on going even if user pulls the plug*

    $i = 1;
    $output = '<p>Refreshing ' . $count . ' resources</p><p>&nbsp;</p>';
    $install->save($output);
    $install->delay($delay);

    foreach ($resources as $resource) {

        $pageId = $resource->get('id');
        $pagetitle = $resource->get('pagetitle');
        $url = $modx->makeUrl($pageId, '', '', 'full');
        /* Avoid infinite loop when requesting this page */
        if (strstr($url, 'refresh-cache' || $modx->$resource->get('id') == $pageId)) {
            continue;
        }

        $output = '<p>(' . $i . ') Refreshing</p><p>' . $pagetitle . '</p>';
        $install->save($output);
        $install->delay($delay);

        $i++;

        curl_setopt($ch, CURLOPT_URL, $url); // Set CURL options

        $unused = curl_exec($ch); // get the page - do nothing with it

        if (curl_errno($ch)) {
            $output = 'cURL error: ' . curl_errno($ch) . " - " . curl_error($ch);
            $install->save($output);
            $install->delay(08.0);
        }
    } /* end foreach($resources) loop */

    curl_close($ch);

    unset($output, $unused, $ch, $pagetitle, $url, $pageId);

    $mtime = microtime();
    $mtime = explode(" ", $mtime);
    $mtime = $mtime[1] + $mtime[0];
    $tend = $mtime;
    $seconds = ($tend - $tstart);
    $totalTime = sprintf( "%02.2d:%02.2d", floor( $seconds / 60 ), $seconds % 60 );
    $install->save("<br />FINISHED -- Execution time (minutes:seconds): {$totalTime}");
    $install->delay($delay);
    $install->clearTemp();

}

return '';
