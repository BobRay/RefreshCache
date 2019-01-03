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
        $nsCorePath = $this->namespaceObj->getCorePath();
        $nsCorePath = empty($nsCorePath)
            ? MODX_CORE_PATH . 'components/' . $this->namespace . '/'
            : $nsCorePath;
        return $this->normalize($nsCorePath);
    }

    public function getComponentAssetsPath() {
        $nsAssetsPath = $this->namespaceObj->getAssetsPath();
        $nsAssetsPath = empty($nsAssetsPath)
            ? MODX_ASSETS_PATH . 'components/refreshcache/'
            : $nsAssetsPath;
        return $this->normalize($nsAssetsPath);
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
            ? MODX_ASSETS_PATH . '/components/refreshcache'
            : $nsAssetsPath;
        $nsAssetsPath = str_replace($this->normalize(dirname(MODX_BASE_PATH)), '', $nsAssetsPath);
        $base = $this->normalize(dirname(MODX_ASSETS_URL)) . '/';
        $short = str_replace($base, '', $this->normalize(MODX_SITE_URL));
        return $short . $nsAssetsPath;
    }

    public function loadCustomCssJs() {
        $namespace = 'refreshcache';
        $this->addJavascript('//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js');
        $this->addLastJavascript($this->componentAssetsUrl . 'js/refreshcache.js');
        $this->addCss($this->componentAssetsUrl . 'css/refreshcache.css');
    }

    public function getTemplate() {
        /** @var $chunk modChunk */

        $managerTheme = $this->modx->getOption('manager_theme', null, 'default', true);
        $fields = array(
            'rc_refreshing' => $this->modx->lexicon('rc_refreshing'),
            'rc_button_message' => $this->modx->lexicon('rc_button_message'),
            'rc_refresh_resource_cache' => $this->modx->lexicon('rc_refresh_resource_cache'),
            'RefreshCache' => $this->modx->lexicon('RefreshCache'),
        );

        $file = $this->componentCorePath . "templates/{$managerTheme}/refreshcache.tpl.html";

        if (! file_exists($file)) {
            $file = $this->componentCorePath . "templates/default/refreshcache.tpl.html";
        }

        if (! file_exists($file)) {
            return "No File: " . $file . "<br>";

        }
        // return file_get_contents($file);
        $chunk = $this->modx->newObject('modChunk');
        $chunk->setCacheable(false);
        $chunk->setContent(file_get_contents($file));
        return $chunk->process($fields);
    }

    public function process(array $scriptProperties = array()) {
        return $this->getTemplate();
    }
}
