<?php

/*
Title: Apinstall 0.0.6
Author: Pawel 'Pavlus' Janisio
Source: http://code.google.com/p/apinstall/
License: GPLv3
*/

class Installer {

    public $steps = 0;
    public $logData = null;
    public $path = '';
    public $logFile = '';
    public $printFile = '';
    public $modx = null;


    public function __construct(&$modx) {
        //we need to do this in case of windows users and usleep function
        set_time_limit(0);

        $this->modx =& $modx;
        $this->path = MODX_ASSETS_PATH . 'components/refreshcache/';
        $this->printFile = $this->path . 'refreshcache.php';
        $this->logFile = $this->path . 'refreshcache.log';
        $this->url = MODX_ASSETS_URL . 'components/refreshcache/refreshcache.php';

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

        echo '<link href="' . MODX_ASSETS_URL . 'components/refreshcache/bar.css" rel="stylesheet" type="text/css" />';


echo "<script type='text/javascript'>
function refresh() {
    $('#apisubmit').fadeOut('slow');
    var intID = setInterval(function () {

        $.ajax({
            type: 'GET',
            url: '" . $this->url . "',
            cache: false,
            success: function (data) {
               $('#apinstall').load('" . $this->url . "?randval=' + Math.random());

               var response = data.toString();
               var searchTerm = 'FINISHED';

               if (data != undefined) {
                   if (response.indexOf(searchTerm) != -1) {
                       clearInterval(intID);
                       $('#apisubmit').fadeIn('slow');
                   }
               }
            },
            error: function (request, status, error) {
               if (status == 404) {
                   clearInterval(intID);
                   alert(request.responseText);
               }
            }
        });

    }, 800);
}
$(document).ready(function () {
    $('#apiform').submit(function () {
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

    public function defineBar($barColour = NULL, $textColour = NULL) {


        if (isset($barColour))
            $this->colour = $barColour;
        else $this->colour = '#84AEBE';

        if (isset($textColour))
            $this->colour = $textColour;
        else $this->colour = '#84AEBE';

        $fp = fopen($this->printFile, "a+");
        $data = '<?php

$steps = ' . $this->steps . ';
$lines = count(file("' . $this->logFile . '"));

$width = round(($lines/' . $this->steps . ')*100,1);
?>

<div class="meter-wrap">
    <div class="meter-value" style="background-color: ' . $this->colour . '; width: <?php echo $width; ?>%;">
        <div class="meter-text">
        <?php echo $width; ?> %
        </div>
    </div>
</div>

<?php

$f = file("' . $this->logFile . '");
?>
<div class="output-text" style="color: ' . $this->colour . '">
<?php
if (isset($f[$lines - 1])) {
echo $f[$lines - 1];
}
?>
</div>';

        $fw = fwrite($fp, $data); //save
        fclose($fp);


    }

    public function delay($sec) {
        $sleepTime = abs($sec);

        if ($sleepTime < 1)
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

    public function clearTemp($delete = NULL) {
        // unlink($this->printFile);
        return;
        if ($delete == TRUE) {
            //delete files
            unlink($this->logFile);
            unlink($this->printFile);
        } else {
            //clear temporary files made by our script
            file_put_contents($this->logFile, '');
            file_put_contents($this->printFile, '');
        }
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
