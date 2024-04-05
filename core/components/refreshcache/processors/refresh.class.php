<?php

/** @var modX $modx */

/* Set MODX_CORE_PATH */
if (!defined('MODX_CORE_PATH')) {
    /* For dev env. */
    @include_once dirname(__FILE__, 7) . '/config.core.php';

    if (!defined('MODX_CORE_PATH')) {
        @include dirname(__FILE__, 4) . '/config.core.php';
    }
}

if (!defined('MODX_CORE_PATH')) {
    echo "\nCould not find config.core.php file";
    exit;
}
/* For MODX 2 */
if (file_exists(MODX_CORE_PATH . 'model/modx/modprocessor.class.php')) {
    include_once MODX_CORE_PATH . 'model/modx/modprocessor.class.php';
}

$version = @ include_once MODX_CORE_PATH . "docs/version.inc.php";
$isModx3 = $version['version'] >= 3;

if ($isModx3) {
    abstract class tempRCprocessor extends MODX\Revolution\Processors\ModelProcessor {
        protected string $prefix = 'MODX\Revolution\\';
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

        $doc = $this->getResource('id', $id);
        $delay = $this->modx->getOption('refreshcache_request_delay',
            null, 0, true);
        usleep((int)$delay * 1000);

        set_time_limit($this->maxExecutionTime);

        $this->modx->resource = $doc;
        $displayErrors = ini_set('display_errors', 0);
        $errorLevel = error_reporting(0);

        /* Restore error settings and context */
        if ($displayErrors) {
            ini_set('display_errors', $displayErrors);
        }
        $this->modx->context->set('key', $oldCtx);
        error_reporting($errorLevel);

        return (json_encode((array('success' => true))));
    }

    /**
     * Modified version of this method in the modRequest class.
     * Gets a requested resource and all required data.
     *
     * @param string $method The method, 'id', or 'alias', by which to perform
     * the resource lookup.
     * @param string|integer $identifier The identifier with which to search.
     * @param array $options An array of options for the resource fetching
     * @return modResource The requested modResource instance or request
     * is forwarded to the error page, or unauthorized page.
     */

    public function getResource($method, $identifier, array $options = array()) {
        /* First two args should always be 'id', and Resource ID (int) */
        /** @var modContentType $contentType */
        $resource = null;
        if ($method == 'alias') {
            die('method should always be "id"');
        } else {
            $resourceId = (int)$identifier;
        }

        if (!is_numeric($resourceId)) {
            $this->modx->sendErrorPage();
        }
        $isForward = false;
        $fromCache = false;

        $cacheKey = $this->modx->context->get('key') . "/resources/{$resourceId}";
        $cachedResource = $this->modx->cacheManager->get($cacheKey, array(
            xPDO::OPT_CACHE_KEY => $this->modx->getOption('cache_resource_key', null, 'resource'),
            xPDO::OPT_CACHE_HANDLER => $this->modx->getOption('cache_resource_handler', null, $this->modx->getOption(xPDO::OPT_CACHE_HANDLER)),
            xPDO::OPT_CACHE_FORMAT => (integer)$this->modx->getOption('cache_resource_format', null, $this->modx->getOption(xPDO::OPT_CACHE_FORMAT, null, xPDOCacheManager::CACHE_PHP)),
        ));
        if (is_array($cachedResource) && array_key_exists('resource', $cachedResource) && is_array($cachedResource['resource'])) {
            /** @var modResource $resource */
            $resource = $this->modx->newObject($cachedResource['resourceClass']);
            if ($resource) {
                $resource->fromArray($cachedResource['resource'], '', true, true, true);
                $resource->_content = $cachedResource['resource']['_content'];
                $resource->_isForward = $isForward;
                if (isset($cachedResource['contentType'])) {
                    $contentType = $this->modx->newObject($this->prefix . 'modContentType');
                    $contentType->fromArray($cachedResource['contentType'], '', true, true, true);
                    $resource->addOne($contentType, 'ContentType');
                }
                if (isset($cachedResource['resourceGroups'])) {
                    $rGroups = array();
                    foreach ($cachedResource['resourceGroups'] as $rGroupKey => $rGroup) {
                        $rGroups[$rGroupKey] = $this->modx->newObject('modResourceGroupResource', $rGroup);
                    }
                    $resource->addMany($rGroups);
                }
                if (isset($cachedResource['policyCache'])) {
                    $resource->setPolicies(array($this->modx->context->get('key') => $cachedResource['policyCache']));
                }
                if (isset($cachedResource['elementCache'])) {
                    $this->modx->elementCache = $cachedResource['elementCache'];
                }
                if (isset($cachedResource['sourceCache'])) {
                    $this->modx->sourceCache = $cachedResource['sourceCache'];
                }
                if ($resource->get('_jscripts')) {
                    $this->modx->jscripts = $this->modx->jscripts + $resource->get('_jscripts');
                }
                if ($resource->get('_sjscripts')) {
                    $this->modx->sjscripts = $this->modx->sjscripts + $resource->get('_sjscripts');
                }
                if ($resource->get('_loadedjscripts')) {
                    $this->modx->loadedjscripts = array_merge($this->modx->loadedjscripts, $resource->get('_loadedjscripts'));
                }
                $isForward = $resource->_isForward;
                $fromCache = true;
            }
        }
        if (!$fromCache || !is_object($resource)) {
            $criteria = $this->modx->newQuery('modResource');
            $criteria->select(array($this->modx->escape('modResource') . '.*'));
            $criteria->where(array('id' => $resourceId));

            if ($resource = $this->modx->getObject($this->prefix . 'modResource', $criteria)) {
                $resource->_isForward = $isForward;

                if ($tvs = $resource->getMany('TemplateVars', 'all')) {
                    /** @var modTemplateVar $tv */
                    foreach ($tvs as $tv) {
                        $resource->set($tv->get('name'), array(
                            $tv->get('name'),
                            $tv->getValue($resource->get('id')),
                            $tv->get('display'),
                            $tv->get('display_params'),
                            $tv->get('type'),
                        ));
                    }
                }
                $this->modx->resourceGenerated = true;

            }
        } elseif ($fromCache && false) { // should be unnecessary
            if (($resource->get('published') || ($this->modx->getSessionState() === modX::SESSION_STATE_INITIALIZED && $this->modx->hasPermission('view_unpublished')))) {
                if ($resource->get('context_key') !== $this->modx->context->get('key')) {
                    if (!$isForward || ($isForward && !$this->modx->getOption('allow_forward_across_contexts', $options, false))) {
                        if (!$this->modx->getCount('modContextResource', array($this->modx->context->get('key'), $resourceId))) {
                            return null;
                        }
                    }
                }
            } else {
                return null;
            }
        }
        $resource->setProcessed(true);
        $cm = $this->modx->getCacheManager();
        @$cm->generateResource($resource);

        return $resource;
    }
}

return 'refreshcacheRefreshProcessor';
