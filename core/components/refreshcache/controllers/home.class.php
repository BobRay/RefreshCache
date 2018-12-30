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
    public $componentCorePath = '';
    public $componentAssetsPath = '';
    public $componentAssetsUrl = '';
    public $namespace = 'refreshcache';
    /** @var $namespaceObj modNamespace */
    public $namespaceObj = null;

    public function getPageTitle() {
        return 'RefreshCache';
    }

    public function getLanguageTopics() {
        return array('refreshcache:default');
    }

    public function initialize() {
        $this->modx->lexicon->load('refreshcache:default');

        $this->namespaceObj = $this->modx->getObject('modNamespace', array('name' => $this->namespace));
        $this->componentCorePath = $this->getComponentCorePath();
        $this->componentAssetsPath = $this->getComponentAssetsPath();
        $this->componentAssetsUrl = $this->getComponentAssetsUrl();
    }

    public function normalize($path) {
        if (strpos($path, '\\') === false) {
            /* Nothing to do */
            return $path;
        } else {
            return str_replace('\\', '/', $path);
        }
    }

    public function getComponentCorePath() {
        $nsCorePath = $this->namespaceObj->get('path');
        $nsCorePath = empty($nsCorePath)
            ? '{core_path}components/refreshcache/'
            : $nsCorePath;
        return $this->normalize(str_replace('{core_path}', MODX_ASSETS_PATH, $nsCorePath));
    }

    public function getComponentAssetsPath() {
        $nsAssetsPath = $this->namespaceObj->get('assets_path');
        $nsAssetsPath = empty($nsAssetsPath)
            ? '{assets_path}components/refreshcache/'
            : $nsAssetsPath;
        return $this->normalize(str_replace('{assets_path}', MODX_ASSETS_PATH, $nsAssetsPath));
    }

    /**
     * Gets Component Assets URL based on assets_path
     * field of namespace
     * @param $namespace
     * @return string
     */
    public function getComponentAssetsUrl() {

        $nsAssetsPath = $this->componentAssetsPath;
        $nsAssetsPath = empty($nsAssetsPath)
            ?  '{assets_path}components/refreshcache/'
            : $nsAssetsPath;
        $nsAssetsPath = $this->normalize(str_replace('{assets_path}', MODX_ASSETS_PATH, $nsAssetsPath));
        $nsAssetsPath = str_replace($this->normalize(dirname(MODX_BASE_PATH)), '', $nsAssetsPath);
        $base = $this->normalize(dirname(MODX_ASSETS_URL)) . '/';
        $short = str_replace($base, '', $this->normalize(MODX_SITE_URL));
        return $short . $nsAssetsPath;
    }

    public function loadCustomCssJs() {
        $namespace = 'refreshcache';
        $this->addJavascript('//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js');
        $this->addJavascript($this->componentAssetsUrl . 'js/refreshcache.js');
        $this->addCss($this->componentAssetsUrl . 'css/refreshcache.css');
    }

    /* ToDo: move to file or template; move CSS to CSS file; Add new language strings */

    public function process(array $scriptProperties = array()) {
        // return $this->getTemplate();
        $buttonText = $this->modx->lexicon('rc_button_message');
        $output = '
<div class="container">
    <h2  class="modx-page-header">RefreshCache</h2>
 
    <div class="x-panel-body shadowbox">
        <div class="panel-desc">Refresh Resource Cache</div>
        <div class="x-panel main-wrapper">
            <fieldset id="refreshcache_fieldset">
                <br class="clear"/>
                <br class="clear">
                <div class="refreshcache_submit">
                    <input class="x-btn x-btn-text" type="submit" id="refreshcache_submit" 
                        name="refreshcache_submit" value="' . $buttonText . '"/>
                </div>
                <div id="refreshcache_results">
                    <div id="progressBar"><div></div></div>
                    <div class="refresh_cache_pagetitle"></div>
                </div>
            </fieldset>

        </div>
    </div>
</div>';
        return $output;
    }
}
