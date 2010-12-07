<?php
/**
 * Loads the TV panel for the resource page.
 *
 * Note: This page is not to be accessed directly.
 *
 * @package modx
 * @subpackage manager
 */

//if (!$modx->hasPermission('quip.thread_view')) return $modx->error->failure($modx->lexicon('access_denied'));
/*
$resourceClass= isset ($_REQUEST['class_key']) ? $_REQUEST['class_key'] : 'modDocument';
$resourceClass = $modx->sanitizeString($resourceClass);
$resourceClass = str_replace(array('../','..','/','\\'),'',$resourceClass);
$resourceDir= strtolower(substr($resourceClass, 3));

$resourceId = isset($_REQUEST['resource']) ? intval($_REQUEST['resource']) : 0;

$onResourceTVFormPrerender = $modx->invokeEvent('OnResourceTVFormPrerender',array(
    'resource' => $resourceId,
));
if (is_array($onResourceTVFormPrerender)) {
    $onResourceTVFormPrerender = implode('',$onResourceTVFormPrerender);
}
*/

//print_r($modx->boerse->config);


//print_r($tabs);

$modx->getService('smarty','smarty.modSmarty'); 

if (! isset ($modx->smarty)){
    $modx->getService('smarty', 'smarty.modSmarty', '', array (
    'template_dir'=>$modx->getOption('manager_path').'templates/'.$modx->getOption('manager_theme', null, 'default').'/',
    ));
}
$modx->smarty->template_dir = $modx->getOption('manager_path').'templates/'.$modx->getOption('manager_theme', null, 'default').'/';

$modx->smarty->assign('OnResourceTVFormPrerender',$onResourceTVFormPrerender);
/*
$delegateView= dirname(__FILE__) . '/' . $resourceDir . '/' . basename(__FILE__);
if (file_exists($delegateView)) {
    $overridden= include_once ($delegateView);
    if ($overridden !== false) {
        return;
    }
}
*/
//get dataobject:
//if (empty($scriptProperties['object_id'])) return $modx->error->failure('oehh..');

$task=$modx->xdbedit->getTask();
$getObject= dirname(dirname(__FILE__)) . '/' . $task . '/' . basename(__FILE__);
if (file_exists($getObject)) {
    $overridden= include_once ($getObject);
    if ($overridden !== false) {
       // return;
    }
}



//$object = $modx->getObject('Angebote',$scriptProperties['angebot']);
if (empty($object)) return $modx->error->failure($modx->lexicon('quip.thread_err_nf'));
//if (!$thread->checkPolicy('view')) return $modx->error->failure($modx->lexicon('access_denied'));

//return $modx->error->success('',$angebot);

//echo '<pre>'.print_r($angebot->toArray(),1).'</pre>';

$modx->xdbedit->loadConfigs();
$tabs=$modx->xdbedit->getTabs();
$fieldid=0;

foreach ($tabs as $tabid=>$tab){
$emptycat = $modx->newObject('modCategory');
$emptycat->set('category',$tab['caption']);
$emptycat->id = $tabid;
$categories[$tabid] = $emptycat;	
	
	$fields=$tab['fields'];
	foreach ($fields as $field){
		
		
		$fieldid++;
		//echo $angebot->get($field['field']);
		if ($tv = $modx->getObject('modTemplateVar',array('name'=>$field['inputTV']))){
			
		}else{
			$tv = $modx->newObject('modTemplateVar');
		}
        //echo '<pre>'.print_r($tv->toArray(),1).'</pre>';
		$fieldvalue = $object->get($field['field']);
        //if (!empty($fieldvalue)){}
		$tv->set('value',$fieldvalue);
		$tv->set('caption',$field['caption']);
		$tv->set('id',$fieldid);
		
            $default = $tv->processBindings($tv->get('default_text'),$resourceId);
            if (strpos($tv->get('default_text'),'@INHERIT') > -1 && (strcmp($default,$tv->get('value')) == 0 || $tv->get('value') == null)) {
                $tv->set('inherited',true);
            }
            if ($tv->get('value') == null) {
                $v = $tv->get('default_text');
                if ($tv->get('type') == 'checkbox' && $tv->get('value') == '') {
                    $v = '';
                }
                $tv->set('value',$v);
            }
           
			
            if ($tv->type == 'richtext') {
                if (is_array($replace_richtexteditor))
                    $replace_richtexteditor = array_merge($replace_richtexteditor, array (
                        'tv' . $tv->id
                    ));
                else
                    $replace_richtexteditor = array (
                        'tv' . $tv->id
                    );
            }
				
			//$inputForm = $tv->renderInput($resource->id);
		
		$modx->smarty->assign('tv',$tv);	
			
        $params= array ();
        if ($paramstring= $tv->get('display_params')) {
            $cp= explode("&", $paramstring);
            foreach ($cp as $p => $v) {
                $v= trim($v);
                $ar= explode("=", $v);
                if (is_array($ar) && count($ar) == 2) {
                    $params[$ar[0]]= $tv->decodeParamValue($ar[1]);
                }
            }
        }
        
		$value= $tv->get('value');
        if ($value === null) {
            $value= $tv->get('default_text');
        }		
        /* find the correct renderer for the TV, if not one, render a textbox */
        $inputRenderPaths = $tv->getRenderDirectories('OnTVInputRenderList','input');
        $inputForm = $tv->getRender($params,$value,$inputRenderPaths,'input',$resourceId,$tv->get('type'));			
           

            if (empty($inputForm)) continue;

            $tv->set('formElement',$inputForm);
			
            if (!is_array($categories[$tabid]->tvs)) {
                $categories[$tabid]->tvs = array();
            }
            $categories[$tabid]->tvs[] = $tv;			
        
	}
}
$modx->smarty->assign('customconfigs',$modx->xdbedit->customconfigs);
$modx->smarty->assign('object',$object);
$modx->smarty->assign('categories',$categories);

if (!empty($_REQUEST['showCheckbox'])) {
    $modx->smarty->assign('showCheckbox',1);
}

$modx->smarty->template_dir = $modx->xdbedit->config['corePath'].'templates/';
return $modx->smarty->fetch('mgr/angebote/fields.tpl');