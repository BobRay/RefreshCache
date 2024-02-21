<?php

/** @var modX $modx */

if (file_exists(MODX_CORE_PATH . 'model/modx/modprocessor.class.php')) {
    include_once MODX_CORE_PATH . 'model/modx/modprocessor.class.php';
}

class refreshcacheRefreshProcessor extends modProcessor {

    public $classKey = 'modResource';
    public $defaultSortField = 'pagetitle';
    public $defaultSortDirection = 'asc';
    public $objectType = 'modResource';
    public $namespace = 'refreshcache';
    public $maxExecutionTime;
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
        $this->maxExecutionTime = ini_get('max_execution_time');
        parent::initialize();
        return true;
    }

    public function process(array $scriptProperties = array()) {
        $uri = MODX_SITE_URL . $this->getProperty('uri');
        $errorLevel = error_reporting();
        error_reporting($errorLevel & ~E_DEPRECATED);

        $id = $this->getProperty('id');
        $doc = $this->modx->getObject('modResource', $id);
        $delay = $this->modx->getOption('refreshcache_request_delay', null, 0, true);
        usleep((int)$delay * 1000);
        set_time_limit($this->maxExecutionTime);
        error_reporting($errorLevel);
        return (json_encode((array('success' => true))));
    }
}

return 'refreshcacheRefreshProcessor';
