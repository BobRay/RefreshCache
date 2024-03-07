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

        $this->modx->resource = $doc;
        // $this->refreshResource_curl($doc);

        $this->refreshResource_generateResource($doc);
        $this->modx->context->set('key', $oldCtx);

        /* restore error level reporting */
        error_reporting($errorLevel);
        return (json_encode((array('success' => true))));
    }

    public function refreshResource_generateResource(&$doc, $options = array()) {
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

        $cm = $this->modx->getCacheManager();
        @$doc->process();
        @$cm->generateResource($doc, $options);
    }

    /** Old method, no longer used */
    // public function refreshResource_curl($doc){
      //  $ch = curl_init(); // Initialize Curl
       // if ($ch === false) {
       //     $this->modx->log(modx::LOG_LEVEL_ERROR,'[RefreshCache]' .
    // $this->modx->lexicon("rc_no_curl")
       //     );
            //$install->save($output);
            // $install->delay(3);
        // }
        // if (!$this->modx->user->hasSessionContext('web')) {
           // $this->modx->user->addSessionContext('web');
        //}

    // @curl_setopt($ch, CURLOPT_NOBODY, TRUE);
    // @curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // @curl_setopt($ch, CURLOPT_USERAGENT,
    //     "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");

    // don't use a cached version of the url
    // @curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);

        // keep on going even if user pulls the plug
        // ignore_user_abort(true);

        // $i = 1;

        // @var $doc modResource
        // $pageId = $doc->get('id');
        // $pagetitle = $resource->get('pagetitle');
        // $url = $this->modx->makeUrl($pageId, '', '', 'full');

        // Avoid infinite loop when requesting this page
        // if (strstr($url, 'refresh-cache' || $
        //      this->modx->$resource->get('id') == $pageId)) {
            //continue;
       // }

       // $output = '<p>(' . $i . '/' . $count . ") " . $refreshingMsg .
        // "</p><p>" . $pagetitle . '</p>';
        // $install->save($output);
        //$install->delay($delay);

       // $i++;

        // Set CURL options
        // curl_setopt($ch, CURLOPT_URL, $url);

        // $unused = curl_exec($ch); // get the page - do nothing with it

//        if (curl_errno($ch)) {
//            $output = 'cURL error: ' . curl_errno($ch) . " - " .
//                curl_error($ch);
//            $install->save($output);
//            $install->delay($delay);*/
//        }
//        curl_close($ch);
//    }


}

return 'refreshcacheRefreshProcessor';
