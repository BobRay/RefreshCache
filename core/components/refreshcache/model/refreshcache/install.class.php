<?php
/**
 * Installer class file for RefreshCache extra
 *
 * Copyright 2011-2013 by Bob Ray <http://bobsguides.com>
 * Created on 07-15-2013
 *
 * RefreshCache is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * RefreshCache is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * RefreshCache; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package refreshcache
 */

/** Adapted from Apinstall 0.0.6
 * Author: Pawel 'Pavlus' Janisio
 * Source: http://code.google.com/p/apinstall/
 * License: GPLv3
 * */


class Installer {

    public $steps = 0;
    public $logData = null;
    public $path = '';
    public $logFile = '';
    public $printFile = '';
    /** @var $modx modX */
    public $modx = null;


    public function __construct(&$modx) {
        //we need to do this in case of windows users and usleep function
        set_time_limit(0);

        $this->modx =& $modx;
        $this->path = MODX_ASSETS_PATH . 'components/refreshcache/';
        $this->printFile = $this->path . 'refreshcache.php';
        $this->logFile = $this->path . 'refreshcache.log';
        $url = MODX_ASSETS_URL . 'components/refreshcache/refreshcache.php';
        /* Get ajax_delay from System Setting */
        $ajaxDelay = $this->modx->getOption('refreshcache_ajax_delay', 900);
        if (file_exists($this->printFile)) {
            file_put_contents($this->printFile, '');
        } else {
            $fp = fopen($this->printFile, 'w');
            fclose($fp);
        }
        if (file_exists($this->logFile)) {
            file_put_contents($this->logFile, '');
        } else {
            $fp = fopen($this->logFile, 'w');
            fclose($fp);
        }


        if (!is_dir($this->path)) {
            mkdir($this->path, true);
        }

        //include css file
        $cssUrl = $this->modx->getOption('refresh_cache_assets_url', null,
                $this->modx->getOption('assets_url') . 'components/refreshcache/')
                .'css/refreshcache.css?v=1.2.0';
        $this->modx->regClientCSS($cssUrl);




        echo "<script type='text/javascript'>
function refresh() {

        Ext.Ajax.request({

            url: '" . $url . "?randval=' + Math.random(),
            success: function (data) {
                var response = data.responseText;
                var searchTerm = 'FINISHED';

                Ext.fly('apinstall').update(response);

                if (response != undefined) {
                   if (response.indexOf(searchTerm) != -1) {
                       Ext.fly('apisubmit').fadeIn({duration:1.5});
                       return;
                   }
                }
                setTimeout(function() {
                    refresh();
                }, " . $ajaxDelay . ");
            },
            error: function (request, status, error) {
               if (status == 404) {
                   clearTimeout(intID);
                   alert(error);
                   return;
               }
            }
        });


}
Ext.onReady(function () {
    Ext.fly('apiform').on('submit', function () {
        Ext.fly('apisubmit').fadeOut({duration:1.5});
        refresh();
    });

});
</script>";
    }


    public function setSteps($count) {
        $this->steps = $count;
        return $this->steps;
    }

    public function showSteps() {

        return $this->steps;
    }

    public function placeholder($iframeName = NULL) {

        if (isset($iframeName))
            $this->iframe = $iframeName;
        else
            $this->iframe = 'progressFrame';


        //load progressbar div and iframe needed by chrome and safari
        echo '<iframe style="display: none" name="' . $this->iframe . '"></iframe>';
        echo '<div id="apinstall"></div>';


    }

    public function defineBar() {
        $fp = fopen($this->printFile, "a+");
        $tpl = '<?php

$steps = ' . $this->steps . ';
$lines = count(file("' . $this->logFile . '"));

$width = round(($lines/' . $this->steps . ')*100,1);
?>

<div class="meter-wrap">
    <div class="meter-value" style="width: <?php echo $width; ?>%;">
        <div class="meter-text">
        <?php echo $width; ?> %
        </div>
    </div>
</div>

<?php

$f = file("' . $this->logFile . '");
?>
<div class="output-text">
<?php
if (isset($f[$lines - 1])) {
echo $f[$lines - 1];
}
?>
</div>';

        $fw = fwrite($fp, $tpl); //save
        fclose($fp);
    }

    public function delay($sec) {
        $sleepTime = abs($sec);

        if ($sleepTime < 1)
            /* usleep is in microseconds */
            usleep($sleepTime * 1000000);
        else
            sleep($sleepTime);
    }


    public function save($output) {
        $this->logData = $output;

        $fp = fopen($this->logFile, "a+");
        $fw = fwrite($fp, $this->logData . "\r\n"); //save
        fclose($fp);
        $this->steps++;

    }


    public function __destruct() {
        //unset all variables defined by class
        if (isset($this->logData)) {
            unset($this->steps);
            unset($this->logData);
            unset($this->path);
            unset($this->logFile);
            unset($this->printFile);
        }
    }
}