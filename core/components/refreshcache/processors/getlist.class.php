<?php

if (! defined('MODX_CORE_PATH')) {
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
        // $this->modx->log(modX::LOG_LEVEL_ERROR, 'In Processor');
        return true;
    }

    /*public function process() {
        parent::process();
    }*/

    public function prepareQueryBeforeCount(xPDOQuery $c) {
        $c->select('id,pagetitle,uri');
        $fields = array(
            'deleted:!=' => '1',
            'class_key:!=' => 'modWebLink',
            'published:!=' => '0',
            'AND:class_key:!=' => 'modSymLink',


        );
        $c->where($fields);

        return $c;
    }

    public function prepareQueryAfterCount(xPDOQuery $c) {
        // $c->select('id,pagetitle,uri');

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

    /**
     * Get the data of the query
     * @return array
     */
    public function xgetData() {
        $data = array();
        $limit = intval($this->getProperty('limit'));
        $start = intval($this->getProperty('start'));

        /* query for chunks */
        $c = $this->modx->newQuery($this->classKey);
        $c = $this->prepareQueryBeforeCount($c);
        $data['total'] = $this->modx->getCount($this->classKey, $c);
        $c = $this->prepareQueryAfterCount($c);

        $sortClassKey = $this->getSortClassKey();
        $sortKey = $this->modx->getSelectColumns($sortClassKey, $this->getProperty('sortAlias', $sortClassKey), '', array($this->getProperty('sort')));
        if (empty($sortKey)) {
            $sortKey = $this->getProperty('sort');
        }
        $c->sortby($sortKey, $this->getProperty('dir'));
        if ($limit > 0) {
            $c->limit($limit, $start);
        }

        $data['results'] = $this->modx->getCollection($this->classKey, $c);
        return $data;
    }



}

/*$p = new refreshcacheGetListProcessor($modx);
$p->initialize();
$result = $p->process();
echo $result; */

return 'refreshcacheGetListProcessor';
