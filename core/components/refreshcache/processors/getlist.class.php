<?php

if (!defined('MODX_CORE_PATH')) {
    /* For dev environment */
    include 'c:/xampp/htdocs/addons/assets/mycomponents/instantiatemodx/instantiatemodx.php';
    /** @var modX $modx */
    $modx->log(modX::LOG_LEVEL_ERROR, '[RefreshCache GetList processor] Instantiated MODX');
}


if (file_exists(MODX_CORE_PATH . 'model/modx/modprocessor.class.php')) {
    include_once MODX_CORE_PATH . 'model/modx/modprocessor.class.php';
}

class refreshcacheGetListProcessor extends modObjectGetListProcessor {
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
        $c->select('id,template,pagetitle,uri,context_key');
        $templates = $this->modx->getOption('refreshcache_templates_enabled',
            null, array(), false);
        if ($templates) {
            $templates = explode(',', $templates);
        }
        $fields = array(
            'cacheable:=' => '1',
            'deleted:!=' => '1',
            'class_key:!=' => 'modWebLink',
            'published:!=' => '0',
            'AND:class_key:!=' => 'modSymLink',
        );
        if ((!empty($templates)) && is_array($templates)) {
            $fields = array_merge(array('template:IN' => $templates), $fields);
        }
        $c->where($fields);

        return $c;
    }

    public function prepareRow(xPDOObject $object) {
        $ta = $object->toArray('', false, true, true);
        if (empty($ta['uri'])) {
            $ta['uri'] = $this->modx->makeUrl($ta['id'],
                $ta['context_key'], "", "full");
        }

        return $ta;
    }

    public function getData() {
        $d = parent::getData();
        return $d;
    }
}

return 'refreshcacheGetListProcessor';
