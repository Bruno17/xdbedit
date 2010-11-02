<?php

$config=$modx->xdbedit->customconfigs;
$prefix = $config['prefix'];
$packageName = $config['packageName'];
$tablename = $config['tablename'];

$packagepath = $modx->getOption('core_path') . 'components/'.$packageName.'/';
$modelpath = $packagepath.'model/';

$modx->addPackage($packageName,$modelpath,$prefix);
$classname = strtoupper(substr($tablename, 0, 1)) . substr($tablename, 1) . '';

if ($this->modx->lexicon)
{
    $this->modx->lexicon->load($packageName.':default');
}

if (empty($scriptProperties['object_id'])||$scriptProperties['object_id']=='neu') {
	$object = $modx->newObject($classname);
	$object->set('object_id','neu');
}
else
{
    $c = $modx->newQuery($classname, $scriptProperties['object_id']);
    $c->leftJoin('modResource', 'Resource');
    $c->select('
        `'.$classname.'`.*,
    	`'.$classname.'`.`id` AS `object_id`,
        `Resource`.`pagetitle` AS `firma`
    ');
    $object = $modx->getObject($classname, $c);
}
