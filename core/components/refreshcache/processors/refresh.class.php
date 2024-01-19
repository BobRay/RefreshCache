<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

if (!defined('MODX_CORE_PATH')) {
    /* For dev environment */
    include 'c:/xampp/htdocs/addons/assets/mycomponents/instantiatemodx/instantiatemodx.php';
    $modx->log(modX::LOG_LEVEL_ERROR, 'Instantiated MODX');
}


if (file_exists(MODX_CORE_PATH . 'model/modx/modprocessor.class.php')) {
    include_once MODX_CORE_PATH . 'model/modx/modprocessor.class.php';
}

class refreshcacheRefreshProcessor extends modProcessor {

    public $classKey = 'modResource';
    public $defaultSortField = 'pagetitle';
    public $defaultSortDirection = 'asc';
    public $objectType = 'modResource';
    public $namespace = 'refreshcache';
    /* @var $client \GuzzleHttp\Client */
    public $client = null;

    /**
     * Gets Component Assets URL based on assets_path
     * field of namespace
     * @param $namespace
     * @return string
     */
    public function getComponentCorePath($namespace) {
        $obj = $this->modx->getObject('modNamespace', array('name' => $namespace));
        $errorLevel = error_reporting();
        error_reporting($errorLevel & ~E_DEPRECATED);
        $cp =  $obj->getCorePath() . 'components/' . $namespace . '/';
        error_reporting($errorLevel);
        return $cp;
    }

    public function initialize() {
        $this->client = new \GuzzleHttp\Client();
        parent::initialize();
        return true;
    }

    public function process(array $scriptProperties = array()) {
        $uri = $this->getProperty('uri');
        $errorLevel = error_reporting();
        error_reporting($errorLevel & ~E_DEPRECATED);

        $id = $this->getProperty('id');
        $doc = $this->modx->getObject('modResource', $id);
        $delay = $this->modx->getOption('refreshcache_request_delay', null, 0, true);
        usleep((int)$delay * 1000);

        if (!empty($uri)) {
            if ($doc && $doc->checkPolicy('view_resource') && strpos($doc->get('alias'), 'cache') === false ) {
            try {
                $this->client->head($uri);
            } catch (GuzzleHttp\Exception\ClientException $e) {
                /* These will mainly be unauthorized URLs or 404s */
                if ($this->modx->getOption('refreshcache_log_all_errors', null, false, true)) {
                    $this->modx->log(modX::LOG_LEVEL_ERROR, "Exception: " . $e->getMessage());
                }
            } catch (Exception $e) {
                $this->modx->log(modX::LOG_LEVEL_ERROR, "Exception: " . $e->getMessage());
            }
            } else {
                /* $this->modx->log(modX::LOG_LEVEL_ERROR, 'Skipping unavailable Resource: ' . $id); */
            }
        }
        error_reporting($errorLevel);
        return (json_encode((array('success' => true))));
    }
}

return 'refreshcacheRefreshProcessor';
