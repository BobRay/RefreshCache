<?php

if (! defined('MODX_CORE_PATH')) {
    /* For dev environment */
    include 'c:/xampp/htdocs/addons/assets/mycomponents/instantiatemodx/instantiatemodx.php';
    $modx->log(modX::LOG_LEVEL_ERROR, 'Instantiated MODX');
}
require_once MODX_CORE_PATH . 'model/modx/modprocessor.class.php';

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
        $c->select('id,pagetitle,uri');
        $fields = array(
            'cacheable:=' => '1',
            'deleted:!=' => '1',
            'class_key:!=' => 'modWebLink',
            'published:!=' => '0',
            'AND:class_key:!=' => 'modSymLink',

        );
        $c->where($fields);

        return $c;
    }

    public function prepareRow(xPDOObject $object) {
        $ta = $object->toArray('', false, true, true);
        if (empty($ta['uri'])) {
            $this->modx->makeUrl($ta['id'], "", "", "full");
        } else {
            $ta['uri'] = MODX_SITE_URL . $ta['uri'];
        }
        return $ta;
    }

    public function getData() {
        $d = parent::getData();
        return $d;
    }
}

return 'refreshcacheGetListProcessor';
