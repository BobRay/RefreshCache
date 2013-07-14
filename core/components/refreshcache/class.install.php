<?php

/*
Title: Apinstall 0.0.6
Author: Pawel 'Pavlus' Janisio
Source: http://code.google.com/p/apinstall/
License: GPLv3
*/

class Installer {

    public $steps = 0;
    public $logData = NULL;
    public $path = '';
    public $logFileName = '';
    public $printFileName = '';


    public function __construct($jquery = NULL) {
        //we need to do this in case of windows users and usleep function
        set_time_limit(0);
        //generate random number printfile name
        $this->printFileName = 'refreshcache.php';
        $this->logFileName = 'refreshcache.log';
        $this->path = MODX_ASSETS_PATH . 'components/refreshcache/';
        $this->url = MODX_ASSETS_URL . 'components/refreshcache/refreshcache.php';

        if (!is_dir($this->path)) {
            mkdir($this->path, true);
        }

        //include css file
        echo '<link href="' . MODX_ASSETS_URL . 'components/refreshcache/bar.css" rel="stylesheet" type="text/css" />';

        //include google jQuery libraries
        if (!isset($jquery)) {
            echo '<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.1/jquery.js"></script>';
        } else if ($jquery == TRUE) {
            //echo 'Warning: jQuery libraries are not included!';
        }

        //include jQuery javascript

        echo "<script type='text/javascript'>


        function refresh(){
        $('#apisubmit').fadeOut('slow');
        var intID = setInterval(function() {

        $.ajax({
        type: 'GET',
        url: '" . $this->url . "',
        cache: false,
        success: function(){

        $('#apinstall').load('" . $this->url . "?randval='+ Math.random());


                            },
    error : function (xhr, d, e) {
      if (xhr.status == 404) {
        clearInterval(intID);
        $('#apinstall').show();
      }
    }
 });

    }, 800);
        }
            $(document).ready(function() {
  $('#apiform').submit(function() {
    refresh();
  });

});

        </script>";
    }


    /*public function setLogPath($path) {
        if (! is_dir($path)) {
            mkdir($path, true);
        }

    $this->path = $path.'/'.$this->logFileName = sha1($_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_ADDR']).'.log';
    return $this->path;


    }*/

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

        $fp = fopen($this->path . $this->printFileName, "a+");
        $data = '<?php

$steps = ' . $this->steps . ';
$lines = count(file("' . $this->path . $this->logFileName . '"));

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

$f = file("' . $this->path . $this->logFileName . '");
?>
<div class="output-text" style="color: ' . $this->colour . '">
<?php
echo $f[$lines - 1]."
</div>"
?>';

        $fw = fwrite($fp, $data); //save
        fclose($fp);


    }

    public function delay($sec) {
        return;
        $sleepTime = abs($sec);

        if ($sleepTime < 1)
            usleep($sleepTime * 1000000);
        else
            sleep($sleepTime);

    }


    public function save($output) {
        $this->logData = $output;

        $fp = fopen($this->path . $this->logFileName, "a+");
        $fw = fwrite($fp, $this->logData . "\r\n"); //save
        fclose($fp);
        $this->steps++;

    }

    public function clearTemp($delete = NULL) {
        return;
        if ($delete == TRUE) {
            //delete files
            unlink($this->path);
            unlink($this->printFileName);
        } else {
            //clear temporary files made by our script
            file_put_contents($this->path . $this->logFileName, '');
            file_put_contents($this->path . $this->printFileName, '');
        }
    }


    public function __destruct() {
        //unset all variables defined by class
        if (isset($this->logData)) {
            unset($this->steps);
            unset($this->logData);
            unset($this->path);
            unset($this->logFileName);
            unset($this->printFileName);
        }
    }
}
