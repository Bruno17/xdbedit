<?php
        
        /*
         * the packageName where you have your classes
         * this can be used in processors
         */        
        $this->customconfigs['packageName']='example';
        /*
         * the table-prefix for your package
         */
		$this->customconfigs['prefix']='modx_example_';
        /*
         * the tablename of the maintable
         * this can be used in processors - see example processors
         */
		$this->customconfigs['tablename']='telephonedir';
		/*
		 * xdbedit-taskname
		 * xdbedit uses the grid and the processor-pathes with that name
		 */
		$this->customconfigs['task']='telephonedir';
        /*
         * the caption of xdbedit-form
         */		
		$this->customconfigs['formcaption']='Contact';
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
                    'caption'=>'Contact',
                    'fields'=>array(
                    array(
                        'field'=>'name',
                        'caption'=>'Name'
                    ),
                    array(
                        'field'=>'jobtitle',
                        'caption'=>'Job Title'
                    ),
                    array(
                        'field'=>'region',
                        'caption'=>'Region',
                        'inputTV'=>'region'
                ))),
                array(
                    'caption'=>'CreatedonTestTab',
                    'fields'=>array(
                    array(
                        'field'=>'createdon',
                        'caption'=>'Created On',
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
$classname = $this->getClassName($tablename);

if ($this->modx->lexicon)
{
    $this->modx->lexicon->load($packageName.':default');
}
*/			