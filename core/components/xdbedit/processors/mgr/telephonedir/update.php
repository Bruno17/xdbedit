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
 * Update and Create-processor for xdbedit
 *
 * @package xdbedit
 * @subpackage processors
 */
//if (!$modx->hasPermission('quip.thread_view')) return $modx->error->failure($modx->lexicon('access_denied'));

//return $modx->error->failure('huhu');


if (empty($scriptProperties['object_id'])){
	$updateerror=true;
	$errormsg=$modx->lexicon('quip.thread_err_ns');
	return ;
} 

$config=$modx->xdbedit->customconfigs;
$prefix = $config['prefix'];
$packageName = $config['packageName'];
$tablename = $config['tablename'];

$packagepath = $modx->getOption('core_path') . 'components/'.$packageName.'/';
$modelpath = $packagepath.'model/';

$modx->addPackage($packageName,$modelpath,$prefix);
$classname = $modx->xdbedit->getClassName($tablename);

if ($modx->lexicon)
{
    $modx->lexicon->load($packageName.':default');
}

switch ($scriptProperties['task']) {
	case 'publish':
	    $object = $modx->getObject($classname, $scriptProperties['object_id']);
        $object->set('publishedon', strftime('%Y-%m-%d %H:%M:%S'));
        $object->set('published', '1');
		$unpub=$object->get('unpub_date');
		if($unpub<strftime('%Y-%m-%d %H:%M:%S')){
		    $object->set('unpub_date',NULL);	
		}		
	    break;
	case 'unpublish':
	    $object = $modx->getObject($classname, $scriptProperties['object_id']);
        $object->set('unpublishedon', strftime('%Y-%m-%d %H:%M:%S'));
        $object->set('published', '0');
		$object->set('unpublishedby',$modx->user->get('id'));//feld fehlt noch	    
		$pub=$object->get('pub_date');
		if($pub<strftime('%Y-%m-%d %H:%M:%S')){
		    $object->set('pub_date',NULL);	
		}
		break;
	case 'delete':
	    $object = $modx->getObject($classname, $scriptProperties['object_id']);
        $object->set('deletedon', strftime('%Y-%m-%d %H:%M:%S'));
        $object->set('deleted', '1');
		$object->set('deletedby',$modx->user->get('id')); 
		break;	
	case 'recall':
	    $object = $modx->getObject($classname, $scriptProperties['object_id']);
        $object->set('deleted', '0');
		break;									
    default:
        
		// set context_key and load fields from config-file
		//$modx->xdbedit->context=$scriptProperties['context_key'];
	    $modx->xdbedit->loadConfigs();
        $tabs=$modx->xdbedit->getTabs();
        $fieldid=0;
        $postvalues=array();
        foreach ($tabs as $tabid=>$tab){
	        $fields=$tab['fields'];
	        foreach ($fields as $field){
		        $fieldid++;
		        $value = $scriptProperties['tv'.$fieldid];
        /*
        switch ($tv->get('type')) {
            case 'url':
                if ($scriptProperties['tv' . $row['name'] . '_prefix'] != '--') {
                    $value = str_replace(array('ftp://','http://'),'', $value);
                    $value = $scriptProperties['tv'.$tv->get('id').'_prefix'].$value;
                }
                break;
            default:
                // handles checkboxes & multiple selects elements 
                if (is_array($value)) {
                    $featureInsert = array();
                    while (list($featureValue, $featureItem) = each($value)) {
                        $featureInsert[count($featureInsert)] = $featureItem;
                    }
                    $value = implode('||',$featureInsert);
                }
                break;
        }
        */
                /* handles checkboxes & multiple selects elements */
                if (is_array($value)) {
                    $featureInsert = array();
                    while (list($featureValue, $featureItem) = each($value)) {
                        $featureInsert[count($featureInsert)] = $featureItem;
                    }
                    $value = implode('||',$featureInsert);
                }		
                $postvalues[$field['field']]=$value;		
            }
        }
        if ($scriptProperties['object_id'] == 'neu'){
            $object = $modx->newObject($classname);
            $tempvalues['createdon']=strftime('%Y-%m-%d %H:%M:%S');
			$postvalues['createdby']=$modx->user->get('id');
        }
        else{
            $object = $modx->getObject($classname, $scriptProperties['object_id']);
            if ( empty($object)) return $modx->error->failure($modx->lexicon('quip.thread_err_nf'));
                $postvalues['editedon']=strftime('%Y-%m-%d %H:%M:%S');
				$postvalues['editedby']=$modx->user->get('id');
				$tempvalues['createdon']=$object->get('createdon');
				$tempvalues['publishedon']=$object->get('publishedon');
        }
		//handle published
		if ($postvalues['published']=='1'){
		    $pub=$object->get('published');
			if (empty($pub)){
			    $tempvalues['publishedon']=strftime('%Y-%m-%d %H:%M:%S');
		        $postvalues['publishedby']=$modx->user->get('id');				    	
		    }
		    $unpub=$object->get('unpub_date');
		    if($unpub<strftime('%Y-%m-%d %H:%M:%S')){
		        $postvalues['unpub_date']=NULL;
		    }			
		}
		if ($postvalues['published']=='0'){
		    $pub=$object->get('pub_date');
		    if($pub<strftime('%Y-%m-%d %H:%M:%S')){
		        $postvalues['pub_date']=NULL;
		    }
		}
        
		/* alias creation:
		$resource=$modx->newObject('modResource');
        $oldalias = $object->get('alias');
		if (empty($oldalias)) {
			$oldalias='';
			$tempvalues['alias'] = $resource->cleanAlias($postvalues['pagetitle']);
        }
		else{
			$tempvalues['alias'] = $oldalias;
		} 
        */			
        //overwrites
		if (empty($postvalues['ow_createdon'])){
			$postvalues['createdon']=$tempvalues['createdon'];
		}
		if (empty($postvalues['ow_publishedon'])){
			$postvalues['publishedon']=$tempvalues['publishedon'];
		}		
        /* handle alias
		if (empty($postvalues['ow_alias'])) {
			
			$postvalues['alias'] = $tempvalues['alias']; 
        }
		else{
			//if posted empty alias generate new one from pagetitle
			if (empty($postvalues['alias'])) {
			    $postvalues['alias'] = $resource->cleanAlias($postvalues['pagetitle']);
            }
		    else{
			    $postvalues['alias'] = $resource->cleanAlias($postvalues['alias']);
		    } 			
		}
		//if new alias was created check if same alias exists for same day

		    //$configs['classname']=$classname;
			$getnews = $modx->getService('getnews','Getnews',$modx->getOption('core_path').'components/newsandmore/model/newsandmore/',$configs);    	
            $createdon = strtotime($postvalues['createdon']);
			
			$params['year']=strftime('%Y', $createdon);
			$params['month']=strftime('%m', $createdon);
			$params['day']=strftime('%d', $createdon);
			$params['alias']=$postvalues['alias'];
			$params['published']='all';
			$params['deleted']='all';
			$params['exclude']=$scriptProperties['object_id'];
			
			$existingobject=$getnews->getpage($params);
			if ($getnews->lastcount>0){
			    $updateerror=true;
				$errormsg='
				Objekt konnte nicht gespeichert werden!<br/>
			    Der Alias ist nicht eindeutig für dieses Erstellungsdatum<br/>
			    Bitte manuell einen eindeutigen Alias eintragen.<br/>
				alias: '.$postvalues['alias'].'<br/>
				Erstellungsdatum: '.strftime('%d.%m.%Y', $createdon).'<br/>
				
			    ';
			    return ;						
			}
    	
				
        unset($resource);
        */
		//$postvalues['context_key']=$scriptProperties['context_key'];	

		$object->fromArray($postvalues);
    }



if ($object->save() == false) {
    $updateerror=true;
	$errormsg=$modx->lexicon('quip.thread_err_save');
	return ;
}

    
//clear cache
$paths = array(
    'config.cache.php',
    'sitePublishing.idx.php',
    'registry/mgr/workspace/',
    'lexicon/',
);
$contexts = $modx->getCollection('modContext');
foreach ($contexts as $context) {
    $paths[] = $context->get('key') . '/';
}

$options = array(
    'publishing' => 1,
    'extensions' => array('.cache.php', '.msg.php', '.tpl.php'),
);
if ($modx->getOption('cache_db')) $options['objects'] = '*';
$results= $modx->cacheManager->clearCache($paths, $options);	

?>
