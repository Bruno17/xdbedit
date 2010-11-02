<?php
        
        /*
         * the packageName where you have your classes
         * this can be used in processors
         */        
        $this->customconfigs['packageName']='stellenboerse';
        /*
         * the table-prefix for your package
         */
		$this->customconfigs['prefix']='modx_boerse_';
        /*
         * the tablename of the maintable
         * this can be used in processors - see example processors
         */
		$this->customconfigs['tablename']='angebote';
		/*
		 * xdbedit-taskname
		 * xdbedit uses the grid and the processor-pathes with that name
		 */
		$this->customconfigs['task']='angebote';
        /*
         * the caption of xdbedit-form
         */		
		$this->customconfigs['formcaption']='Stellenangebot';
		/*
		 * the tabs and input-fields for your xdbedit-page
		 * outerarray: caption for Tab and fields
		 * innerarray of fields:
		 * field - the tablefield
		 * caption - the form-caption for that field
		 * inputTV - the TV which is used as input-type
		 * without inputTV or if not found it uses text-type
		 * 
		 */
		$this->customconfigs['tabs']=			
			array(
                array(
                    'caption'=>'Allgemein',
                    'fields'=>array(
                    array(
                        'field'=>'firma_resource',
                        'caption'=>'Firma',
                        'inputTV'=>'firma'
                    ),
                    array(
                        'field'=>'pagetitle',
                        'caption'=>'Titel'
                    ),
                    array(
                        'field'=>'stellenanzahl',
                        'caption'=>'Anzahl Stellen',
                    ),
                    array(
                        'field'=>'type',
                        'caption'=>'Typ',
                        'inputTV'=>'stellenangebot_typ'
                ))),
                array(
                    'caption'=>'Content',
                    'fields'=>array(
                    array(
                        'field'=>'content',
                        'caption'=>'Beschreibung',
                        'inputTV'=>'strasse'
                ))),
                array(
                    'caption'=>'Test',
                    'fields'=>array(
                    array(
                        'field'=>'createdon',
                        'caption'=>'Erstellt am',
						'inputTV'=>'datum'
                ))));

/*
* here you can load your package(s) or in the processors
* 
*/
/*
$prefix = $this->customconfigs['prefix'];
$packageName = $this->customconfigs['packageName'];
       
$packagepath = $modx->getOption('core_path') . 'components/'.$packageName.'/';
$modelpath = $packagepath.'model/';

$modx->addPackage($packageName,$modelpath,$prefix);
$classname = strtoupper(substr($tablename, 0, 1)) . substr($tablename, 1) . '';

if ($this->modx->lexicon)
{
    $this->modx->lexicon->load($packageName.':default');
}
*/			