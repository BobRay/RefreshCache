<?php

/** @var modX $modx */

/* Set MODX_CORE_PATH */
include dirname(__FILE__, 5) . '/config.core.php';

/* For MODX 2 */
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

    /** @return bool */
    public function initialize() {
        $this->maxExecutionTime = ini_get('max_execution_time');
        parent::initialize();
        return true;
    }

    public function process(array $scriptProperties = array()) {
        /** @var modResource $doc */
        $cm = $this->modx->getCacheManager();

        /* Save old error_reporting level */
        $errorLevel = error_reporting();

        /* Prevent PHP 8 deprecation notices from crashing JS */
        error_reporting($errorLevel & ~E_DEPRECATED);

        /* Options for generateResource call */
        $options = array(
            xPDO::OPT_CACHE_KEY => $this->modx->getOption
            ('cache_db_key', null, 'db'),

            xPDO::OPT_CACHE_HANDLER => $this->modx->getOption
            (xPDO::OPT_CACHE_DB_HANDLER, null, $this->modx->getOption(xPDO::OPT_CACHE_HANDLER, null, 'xPDO\\Cache\\xPDOFileCache')),

            xPDO::OPT_CACHE_FORMAT => (integer)$this->modx->getOption
            ('cache_db_format', null, $this->modx->getOption(xPDO::OPT_CACHE_FORMAT, null, xPDOCacheManager::CACHE_PHP)),

            xPDO::OPT_CACHE_PREFIX => '',
        );
        /* Get Resource ID */
        $id = $this->getProperty('id');

        /* Save original context key */
        $oldCtx = $this->modx->context->get('key');

        /* Get Resource context key */
        $context = $this->getProperty('context');

        /* Set context to prevent caching in 'mgr' dir */
        $this->modx->context->set('key', $context);

        $doc = $this->modx->getObject($this->prefix . 'modResource', $id);

        $delay = $this->modx->getOption('refreshcache_request_delay',
            null, 0, true);
        usleep((int)$delay * 1000);

        set_time_limit($this->maxExecutionTime);

        @$doc->process();
        @$cm->generateResource($doc, $options);

        /* Restore $modx->context */
        $this->modx->context->set('key', $oldCtx);

        /* restore error level reporting */
        error_reporting($errorLevel);
        return (json_encode((array('success' => true))));
    }
}

return 'refreshcacheRefreshProcessor';
