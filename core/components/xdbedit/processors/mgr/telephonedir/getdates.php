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
$mode = $modx->getOption('mode',$scriptProperties,'year');
$region = $modx->getOption('region',$scriptProperties,'all');


$c = $modx->newQuery($classname);
//$count = $modx->getCount($classname,$c);

$execute = true;

switch ($mode){
	case 'year':
        $sort = $modx->getOption('sort',$scriptProperties,'YEAR(`'.$classname.'`.`createdon`)');	
        $dir = $modx->getOption('dir',$scriptProperties,'DESC');
		$c->select('id,YEAR(createdon) as optionname');
	break;
	case 'month':
        if ($scriptProperties['year']=='all'){
        	$rows=array();
			$execute = false;
        }
		else{
		    $sort = $modx->getOption('sort',$scriptProperties,'MONTH(`'.$classname.'`.`createdon`)');	
            $dir = $modx->getOption('dir',$scriptProperties,'ASC');
			$c->select('id,MONTH(createdon) as optionname,YEAR(`'.$classname.'`.`createdon`) as year');
            $c->where("YEAR(" . $modx->escape($classname) . '.' . $modx->escape('createdon') . ") = " .$scriptProperties['year'], xPDOQuery::SQL_AND);								

		}

	break;	
	default:
	break;
}

if ($execute){
if ($region != 'all'){
    $c->where(array($classname.'.region' => $region));
}	
$c->groupby('optionname');
$c->sortby($sort,$dir);
$stmt  = $c->prepare();
//echo $c->toSql();
$stmt->execute();
$rows = $stmt->fetchAll();	
}

$count = count($rows);

$rows = array_merge(array(array('optionname'=>'all')),$rows);
//$c->prepare(); echo $c->toSql();
/*
$collection = $modx->getCollection($classname, $c);
$rows=array();
foreach ($collection as $row){
	$rows[]=$row->toArray();
}
$count=count($rows);
*/
return $this->outputArray($rows,$count);
