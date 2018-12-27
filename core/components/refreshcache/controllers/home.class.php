<?php

/**
 * Created by PhpStorm.
 * User: BobRay
 * Date: 12/23/2018
 * Time: 12:12 AM
 */

if (! defined('MODX_CORE_PATH')) {

}

class refreshcacheHomeManagerController extends modExtraManagerController {
    public function getPageTitle() {
        return 'RefreshCache';
    }

    public function initialize() {
        // $this->modx->log(modX::LOG_LEVEL_ERROR, 'In Controller');
    }


    public function loadCustomCssJs() {
       // $this->modx->log(modX::LOG_LEVEL_ERROR, 'In loadCustomCssJs');
       // parent::loadCustomCSSJs();
        $namespace = 'refreshcache';
        $namespaceObj = $this->modx->getObject('modNamespace',array('name' => $namespace));
        $assetsPath = $namespaceObj->getAssetsPath();
        // $this->modx->log('modX::LOG_LEVEL_ERROR', 'Assets Path: ' . $assetsPath);
        // C:/xampp/htdocs/addons/assets/mycomponents/refreshcache/assets/components/refreshcache/
        $assetsUrl = str_replace('C:/xampp/htdocs/addons/', MODX_SITE_URL, $assetsPath);
        // $this->modx->log('modX::LOG_LEVEL_ERROR', 'Assets URL: ' . $assetsUrl);
        $this->addJavascript('//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js');

        $this->addJavascript($assetsUrl . 'js/refreshcache.js');
    }

    public function  xrender() {
        /*$output = '<h3>XRefreshCache</h3>';
        $output .= '<div class="rc_main_div" style="position:absolute; top:30%; left:20%;">Hello World</div>';

        $output .= '<div id="refresh_cache">
            <input type="submit" id="refresh_cache_button">
            
            <div id="refresh_cache_results"></div>
        
        </div>';

        $output .= '<script>' . include '//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js' . '</script>';
      //  $output .= '<script>' . include '//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js' . '</script >';

        return $output; */
    }

    public function process(array $scriptProperties = array()) {
        // parent::process();
       // $this->modx->log(modX::LOG_LEVEL_ERROR, 'In Process');
        $output = '<div class="container">
    <h2  class="modx-page-header">RefreshCache</h2>
 
    <div class="x-panel-body shadowbox">
        <div class="panel-desc">Refreshed Resources</div>
        <div class="x-panel main-wrapper">
 
 
        <!-- <form action="#" id="refreshcache_form" method="post">-->
            <fieldset id="refreshcache_fieldset" style="padding: 0 30px 70px 30px;">
 
                <!--<div>
                  <label for="search_term">Search For: </label>
                  <input required class="x-form-text x-form-field" size=100 type="text" id="search_term" name="search_term">
                </div>-->
                <br class="clear"/>
 
                <br class="clear">
 
 
                <div class="refreshcache_submit">
                    <input style="padding:5px;" type="submit" id="refreshcache_submit" name="refreshcache_submit" value="Refresh Cache"/>
                </div>
                <div id="refreshcache_results">
                    <div class="refresh_cache_inner"></div>
                </div>
            </fieldset>
        <!--  </form>-->
        </div>
        </div>
</div>';


       /* $output = '<h3>XRefreshCache</h3>';
        $output .= '<div class="rc_main_div" style="position:absolute; top:30%; left:20%;">Hello World</div>';

        $output .= '<div id="refresh_cache">
            <input type="submit" id="refresh_cache_button">
            
            <div id="refresh_cache_results"></div>
        
        </div>';*/

        // $output .= '<script>' . include '//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js' . '</script>';
        //  $output .= '<script>' . include '//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js' . '</script >';
        return $output;

    }
}
