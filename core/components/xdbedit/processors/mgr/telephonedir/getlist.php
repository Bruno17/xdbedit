<?php

//if (!$modx->hasPermission('quip.thread_list')) return $modx->error->failure($modx->lexicon('access_denied'));

$config=$modx->xdbedit->customconfigs;
$prefix = $config['prefix'];
$packageName = $config['packageName'];
$tablename = $config['tablename'];

$packagepath = $modx->getOption('core_path') . 'components/'.$packageName.'/';
$modelpath = $packagepath.'model/';

$modx->addPackage($packageName,$modelpath,$prefix);
$classname = $modx->xdbedit->getClassName($tablename);

if ($this->modx->lexicon)
{
    $this->modx->lexicon->load($packageName.':default');
}

/* setup default properties */
$isLimit = !empty($scriptProperties['limit']);
$isCombo = !empty($scriptProperties['combo']);
$start = $modx->getOption('start',$scriptProperties,0);
$limit = $modx->getOption('limit',$scriptProperties,20);
$sort = $modx->getOption('sort',$scriptProperties,'id');
$dir = $modx->getOption('dir',$scriptProperties,'ASC');
$year = $modx->getOption('year',$scriptProperties,'all');
$month = $modx->getOption('month',$scriptProperties,'all');
$region = $modx->getOption('region',$scriptProperties,'all');
$showtrash = $modx->getOption('showtrash',$scriptProperties,'');

$c = $modx->newQuery($classname);
if ($region != 'all'){
    $c->where(array($classname.'.region' => $region));
}	
if ($year != 'all'){
    $c->where("YEAR(" . $modx->escape($classname) . '.' . $modx->escape('createdon') . ") = " .$year, xPDOQuery::SQL_AND);		
}
if ($month != 'all'){
    $c->where("MONTH(" . $modx->escape($classname) . '.' . $modx->escape('createdon') . ") = " .$month, xPDOQuery::SQL_AND);		
}
if (!empty($showtrash)){
    $c->where(array($classname.'.deleted' => '1'));	
}else{
	$c->where(array($classname.'.deleted' => '0'));	
}
$count = $modx->getCount($classname,$c);

$c->select('
    `'.$classname.'`.*
');
$c->sortby($sort,$dir);
if ($isCombo || $isLimit) {
    $c->limit($limit,$start);
}
//$c->sortby($sort,$dir);
//$c->prepare(); echo $c->toSql();
$collection = $modx->getCollection($classname, $c);
$rows=array();
foreach ($collection as $row){
	$rows[]=$row->toArray();
}

