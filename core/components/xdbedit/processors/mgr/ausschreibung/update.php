<?php

//if (!$modx->hasPermission('quip.thread_view')) return $modx->error->failure($modx->lexicon('access_denied'));



if (empty($scriptProperties['object_id'])) return $modx->error->failure($modx->lexicon('quip.thread_err_ns'));

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

switch ($scriptProperties['task']) {
	case 'publish':
	    $object = $modx->getObject($classname, $scriptProperties['object_id']);
        $object->set('publishedon', strftime('%Y-%m-%d %H:%M:%S'));
        $object->set('published', '1');
		$object->set('publishedby',$modx->user->get('id'));	    
	    break;
	case 'unpublish':
	    $object = $modx->getObject($classname, $scriptProperties['object_id']);
        $object->set('unpublishedon', strftime('%Y-%m-%d %H:%M:%S'));
        $object->set('published', '0');
		$object->set('unpublishedby',$modx->user->get('id'));//feld fehlt noch	    
	    break;		
    default:
        if ($scriptProperties['object_id'] == 'neu'){
            $object = $modx->newObject($classname);
            $object->set('createdon', strftime('%Y-%m-%d %H:%M:%S'));
            $object->set('publishedon', strftime('%Y-%m-%d %H:%M:%S'));
            $object->set('published', '1');
			$object->set('publishedby',$modx->user->get('id'));
			$object->set('createdby',$modx->user->get('id'));
        }
        else{
            $object = $modx->getObject($classname, $scriptProperties['object_id']);
            if ( empty($object)) return $modx->error->failure($modx->lexicon('quip.thread_err_nf'));
                $object->set('editedon', strftime('%Y-%m-%d %H:%M:%S'));
				$object->set('editedby',$modx->user->get('id'));
        }
        
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

        $postvalues[$field['field']]=$scriptProperties['tv'.$fieldid];		
    }
}


        $object->fromArray($postvalues);

}


if ($object->save() == false) {
return $modx->error->failure($modx->lexicon('quip.thread_err_save'));
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

