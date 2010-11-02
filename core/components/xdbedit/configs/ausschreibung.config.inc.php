<?php
       /*
	    $prefix = 'modx_boerse_';
        $this->modx->addPackage('stellenboerse',$this->modx->getOption('core_path').'components/stellenboerse/model/',$prefix);
        if ($this->modx->lexicon) {
            $this->modx->lexicon->load('stellenboerse:default');
        }
		*/

        $this->customconfigs['prefix']='modx_regatta_';
        $this->customconfigs['packageName']='ausschreibung';
        $this->customconfigs['tablename']='ausschreibung';
		$this->customconfigs['task']='ausschreibung';
		$this->customconfigs['formcaption']='Rennen';
		$this->customconfigs['tabs']=			
			array(
                array(
                    'caption'=>'Allgemein',
                    'fields'=>array(
                    array(
                        'field'=>'rennklasse',
                        'caption'=>'Rennklasse'
                    ),
                    array(
                        'field'=>'altersklasse',
                        'caption'=>'Altersklasse'
                    ),
                    array(
                        'field'=>'Bootsklasse',
                        'caption'=>'bootsklasse',
                    
                ))));		