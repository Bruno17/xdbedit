<?php

$region = $modx->getOption('region',$scriptProperties,'all');
$config=$modx->xdbedit->customconfigs;
$prefix = $config['prefix'];
$packageName = $config['packageName'];
$tablename = $config['tablename'];
$tvPrefix = $modx->getOption('tvPrefix',$config,'');
$field_firmenname = $modx->getOption('field_firmenname',$config,'pagetitle');
$tv_firmenname = $modx->getOption('tv_firmenname',$config,'');
$includeTVs = $modx->getOption('includeTVs',$config,$tv_firmenname);
$sort = 'region';
$dir = 'ASC';

$packagepath = $modx->getOption('core_path') . 'components/'.$packageName.'/';
$modelpath = $packagepath.'model/';

$modx->addPackage($packageName,$modelpath,$prefix);
$classname = $modx->xdbedit->getClassName($tablename);

// get regions from table

$c = $modx->newQuery($classname);
//$c->leftJoin('modResource','Resource');
if ($region != 'all'){
$c->where(array($classname.'.region' => $region));
}
$c->groupBy('region');

$c->sortby($sort,$dir);
//$c->prepare(); echo $c->toSql();
$collection = $modx->getCollection($classname, $c);
$regions=array();
$regions[]['name']='all';
foreach ($collection as $row){
	$regions[]['name']=$row->get('region');
}

$count = count($regions);
return $this->outputArray($regions ,$count);