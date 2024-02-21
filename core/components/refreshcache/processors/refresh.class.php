<?php

/** @var modX $modx */

if (file_exists(MODX_CORE_PATH . 'model/modx/modprocessor.class.php')) {
    include_once MODX_CORE_PATH . 'model/modx/modprocessor.class.php';
}

$version = @ include_once MODX_CORE_PATH . "docs/version.inc.php";
$isModx3 = $version['version'] >= 3;

if ($isModx3) {
    abstract class tempRCprocessor extends MODX\Revolution\Processors\ModelProcessor {
        protected string $prefix = 'MODX\REvolution\\';
    }
} else {
    abstract class tempRCprocessor extends modProcessor {
        protected string $prefix = '';
    }
}

class refreshcacheRefreshProcessor extends tempRCprocessor {
    public string $maxExecutionTime;

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
