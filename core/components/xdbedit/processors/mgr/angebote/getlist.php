<?php
/**
 * XdbEdit
 *
 * Copyright 2010 by Bruno Perner <b.perner@gmx.de>
 *
 * This file is part of XdbEdit, for editing custom-tables in MODx Revolution CMP.
 *
 * XdbEdit is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * XdbEdit is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * XdbEdit; if not, write to the Free Software Foundation, Inc., 59 Temple Place,
 * Suite 330, Boston, MA 02111-1307 USA 
 *
 * @package xdbedit
 */
/**
 * Get a items for xdbedit-grid
 *
 * @package xdbedit
 * @subpackage processors
 */
//if (!$modx->hasPermission('quip.thread_list')) return $modx->error->failure($modx->lexicon('access_denied'));

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

/* setup default properties */
$isLimit = !empty($scriptProperties['limit']);
$isCombo = !empty($scriptProperties['combo']);
$start = $modx->getOption('start',$scriptProperties,0);
$limit = $modx->getOption('limit',$scriptProperties,20);
$sort = $modx->getOption('sort',$scriptProperties,'id');
$dir = $modx->getOption('dir',$scriptProperties,'DESC');
$year = $modx->getOption('year',$scriptProperties,'alle');
$month = $modx->getOption('month',$scriptProperties,'alle');

$c = $modx->newQuery($classname);
$c->leftJoin('modResource','Resource');

if ($year != 'alle'){
$c->where("YEAR(" . $modx->escape($classname) . '.' . $modx->escape('createdon') . ") = " .$year, xPDOQuery::SQL_AND);		
}
if ($month != 'alle'){
$c->where("MONTH(" . $modx->escape($classname) . '.' . $modx->escape('createdon') . ") = " .$month, xPDOQuery::SQL_AND);		
}

$count = $modx->getCount($classname,$c);

$c->select('
    `'.$classname.'`.*,
    `Resource`.`pagetitle` AS `firma`
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


