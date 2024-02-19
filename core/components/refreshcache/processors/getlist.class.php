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
    abstract class tempRCGProcessor extends modObjectGetListProcessor {
        protected string $prefix = 'MODX\REvolution\\';
    }
} else {
    abstract class tempRCGProcessor extends modObjectGetListProcessor {
        protected string $prefix = '';
    }
}


if (file_exists(MODX_CORE_PATH . 'model/modx/modprocessor.class.php')) {
    include_once MODX_CORE_PATH . 'model/modx/modprocessor.class.php';
}

class refreshcacheGetListProcessor extends tempRCGProcessor {
    public $classKey = 'modResource';
    public $defaultSortField = 'pagetitle';
    public $defaultSortDirection = 'asc';
    public $objectType = 'modResource';

    public function initialize() {
        parent::initialize();
        $limit = $this->modx->getOption('rc.limit', null, 0, true);
        $this->setProperty('limit', $limit);
        return true;
    }

    public function prepareQueryBeforeCount(xPDOQuery $c) {
        $c->select('id,template,pagetitle,context_key,class_key');
        $c->sortby('alias');
        $templates = $this->modx->getOption
        ('refreshcache_templates_enabled',
            null, array(), false);
        if ($templates) {
            $templates = explode(',', $templates);
        }

        $omits = array(
            'modWebLink', 'modSymLink', 'modStaticResource',
            'Article', 'ArticlesContainer'
        );

        $fields = array(
            'cacheable:=' => '1',
            'deleted:!=' => '1',
            'class_key:Not In' => $omits,
            'published:!=' => '0',
        );
        /* Honor hidemenu of option set */
        $honorHidemenu = $this->modx->getOption('refreshcache_honor_hidemenu', null, false, false);

        /* Add ! hidemenu to criteria if set */
        if ($honorHidemenu) {
            $fields = array_merge(array('hidemenu:!=' => '1'), $fields);
        }

        /* Restrict query to templates if set */
        if ((!empty($templates)) && is_array($templates)) {
            $fields = array_merge(array('template:IN' => $templates), $fields);
        }
        $c->where($fields);
        return $c;
    }

    public function prepareRow(xPDOObject $object) {
        $ta = $object->toArray('', false, true, true);

        return $ta;
    }

    public function getData() {
        $d = parent::getData();
        return $d;
    }
}

return 'refreshcacheGetListProcessor';
