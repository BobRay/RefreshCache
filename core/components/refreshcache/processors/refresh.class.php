<?php
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

if (! defined('MODX_CORE_PATH')) {
    /* For dev environment */
    include 'c:/xampp/htdocs/addons/assets/mycomponents/instantiatemodx/instantiatemodx.php';
    $modx->log(modX::LOG_LEVEL_ERROR, 'Instantiated MODX');
}
require_once MODX_CORE_PATH . 'model/modx/modprocessor.class.php';
require_once MODX_CORE_PATH . 'components/guzzle6/vendor/autoload.php';

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
        return $obj->getCorePath() . 'components/' . $namespace . '/';
    }

    public function initialize() {
        $this->client = new \GuzzleHttp\Client();
        parent::initialize();
        return true;
    }

    public function process(array $scriptProperties = array()) {
        $uri = $this->getProperty('uri');
        $delay = $this->modx->getOption('refreshcache_request_delay', null, 0, true);
        usleep((int) $delay * 1000);

        if (!empty($uri)) {
            try {
                $this->client->head($uri);
            } catch (GuzzleHttp\Exception\ClientException $e) {
                /* Ignore - these will mainly be unauthorized URLs or 404s */
            } catch (Exception $e) {
                $this->modx->log(modX::LOG_LEVEL_ERROR, "Exception: " . $e->getMessage());
            }
        }
        return(json_encode((array('success' => true))));
    }
}

return 'refreshcacheRefreshProcessor';
