<?php

//if (empty($scriptProperties['thread'])) { return ''; }
$boerse = $modx->getService('boerse','Boerse',$corePath.'model/stellenboerse/',$scriptProperties);
if (!($boerse instanceof Boerse)) return '';

$boerse = new Boerse($modx);
$boerse->initialize($modx->context->get('key'));

$output = '';
$params = $modx->request->getParameters();
$placeholders['self'] = $modx->makeUrl($modx->resource->get('id'),'',$params);

/* get default properties */

$tpl = $modx->getOption('tpl',$scriptProperties,'stellenangebot');
$outerTpl = $modx->getOption('outerTpl',$scriptProperties,'stellenangebote');

$useCss = $modx->getOption('useCss',$scriptProperties,true);
$altRowCss = $modx->getOption('altRowCss',$scriptProperties,'quip-comment-alt');
$dateFormat = $modx->getOption('dateFormat',$scriptProperties,'%b %d, %Y at %I:%M %p');
$showWebsite = $modx->getOption('showWebsite',$scriptProperties,true);

$sortBy = $modx->getOption('sortBy',$scriptProperties,'createdon');
$sortByAlias = $modx->getOption('sortByAlias',$scriptProperties,'Angebote');
$sortDir = $modx->getOption('sortDir',$scriptProperties,'DESC');
$limit = $modx->getOption('limit',$scriptProperties,0);
$offset = $modx->getOption('offset',$scriptProperties,0);
$totalVar = $modx->getOption('totalVar',$scriptProperties,'total');

$id = $modx->getOption('id',$scriptProperties,'');
$get_id = empty($id)?'':'_'.$id;

$typ = $modx->getOption('typ',$scriptProperties,false);

$angebotId = $modx->getOption('angebot',$scriptProperties,false); 
$angebotId = isset($_GET[$get_id.'angebot'])?$_GET[$get_id.'angebot']:$angebotId; 

$firmaId = $modx->getOption('firma',$scriptProperties,false); 
$firmaId = isset($_GET[$get_id.'firma'])?$_GET[$get_id.'firma']:$firmaId; 


/* if css, output */
if ($useCss) {
    $modx->regClientCSS($quip->config['css_url'].'web.css');
}


/* get angebote */
$c = $modx->newQuery('Angebote');
//$c->leftJoin('modUser','Author');
$c->leftJoin('modResource','Resource');

$c->select('
    `Angebote`.*,
    `Resource`.`pagetitle` AS `firma`,
    `Resource`.`id` AS `firma_id`
');

if ($angebotId) {
$c->where(array(
    'id' => $angebotId,
));		
}

if ($firmaId){
$c->where(array(
    'firma_resource' => $firmaId,
));	
}
if ($typ){
$c->where(array(
    'type' => $typ,
));	
}
$c->where(array(
    'published' => 1,
));	
$c->where(array(
    'deleted' => 0,
));	

$count = $modx->getCount('Angebote',$c);
$modx->setPlaceholder($totalVar, $count);
$placeholders['total'] = $count;

$c->sortby('`'.$sortByAlias.'`.`'.$sortBy.'`',$sortDir);
if (!empty($limit)) $c->limit($limit, $offset);
$angebote = $modx->getCollection('Angebote',$c);

/* iterate */
$hasAuth = $modx->user->hasSessionContext($modx->context->get('key')) || $modx->getOption('debug',$scriptProperties,false);
$placeholders['angebote'] = '';
$alt = false;
foreach ($angebote as $angebot) {
    $angebotArray = $angebot->toArray();
    if ($alt) { $angebotArray['alt'] = $altRowCss; }
    $angebotArray['createdon_format'] = strftime($dateFormat,strtotime($angebot->get('createdon')));
    $angebotArray['self'] = $placeholders['self'];
	$angebotArray['activeclass'] = (isset($_GET['angebot'])&&$_GET['angebot']==$angebotArray['id'])?'class="active"':''; 
    $placeholders['angebote'] .= $boerse->getChunk($tpl,$angebotArray);
    $alt = !$alt;
    //echo '<pre>'.print_r($angebotArray,1).'</pre>';
}


$modx->toPlaceholders($placeholders,'stellenangebote');
if ($modx->getOption('useWrapper',$scriptProperties,true)) {
    $output = $boerse->getChunk($outerTpl,$placeholders);
}
else $output = $placeholders['angebote'];

if ($angebotId) {
$modx->setPlaceholder('angebotId',$angebotId);
$modx->setPlaceholder('firmaId',$angebotArray['firma_resource']);
$modx->setPlaceholder('firmaName',$angebotArray['firma']);	
}


?>