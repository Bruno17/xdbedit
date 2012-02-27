<?php

$form = '
<form method="post">
packageName:<input type="text" name="packageName" value="'.$_POST['packageName'].'"/><br/>
prefix:<input type="text" name="prefix"/><br/>
tables(optional):<input type="text" name="tableList"/><br/>
Write schema:<input type="checkbox" name="writeSchema"/><br/>
Parse schema:<input type="checkbox" name="parseSchema"/><br/>
Add missing fields to package-tables:<input type="checkbox" name="autoaddfields"/><br/>
Remove deleted fields in package-tables:<input type="checkbox" name="removefields"/><br/>
<input type="submit" name="create">
</form>
';

if (isset($_POST['create'])) {

    $prefix = $_POST['prefix'];
    $packageName = $_POST['packageName'];
    //$tablename = $scriptProperties['tablename'];
    $tableList = isset($_POST['tableList']) && !empty($_POST['tableList']) ? $_POST['tableList'] : null;
    //$tableList = array(array('table1'=>'classname1'),array('table2'=>'className2'));
    $restrictPrefix = true;

    $packagepath = $modx->getOption('core_path') . 'components/' . $packageName .
        '/';
    $modelpath = $packagepath . 'model/';
    $schemapath = $modelpath . 'schema/';
    $schemafile = $schemapath . $packageName . '.mysql.schema.xml';

    // create folders
    if (!is_dir($packagepath)) {
        mkdir($packagepath, 0777);
    }
    if (!is_dir($modelpath)) {
        mkdir($modelpath, 0777);
    }
    if (!is_dir($schemapath)) {
        mkdir($schemapath, 0777);
    }
    $manager = $modx->getManager();
    $generator = $manager->getGenerator();
    if (isset($_POST['writeSchema'])) {

        //Use this to create a schema from an existing database
        $xml = $generator->writeSchema($schemafile, $packageName, 'xPDOObject', $prefix,
            $restrictPrefix, $tableList);

    }

    if (isset($_POST['parseSchema'])) {
        //Use this to generate classes and maps from your schema
        // NOTE: by default, only maps are overwritten; delete class files if you want to regenerate classes
        $generator->parseSchema($schemafile, $modelpath);
    }

    if (isset($_POST['autoaddfields']) || isset( $_POST['removefields'])) {
        $prefix = empty($prefix) ? null : $prefix;
        $options['addmissing'] = $_POST['autoaddfields'] ? 1 : 0; 
        $options['removedeleted'] =  $_POST['removefields'] ? 1 : 0; 
        
        $modx->addPackage($packageName,$modelpath,$prefix);  
        $pkgman = $modx->xdbedit->loadPackageManager();
        
        $pkgman->parseSchema($schemafile, $modelpath,true);

        $pkgman->checkClassesFields($options);
        
    }


    //$modx->addPackage($packageName,$modelpath,$prefix);
    //$classname = strtoupper(substr($tablename, 0, 1)) . substr($tablename, 1) . '';

    /*

    if ($dataobject=$modx->getObject($classname,array('id'=>$_GET['resId']))){
    $hook->setValues($dataobject->toArray());
    

    //$errorMsg = '<pre>'.print_r($dataobject->toArray(),true).'</pre>';  
    //$hook->addError('error_message',$errorMsg);  
    }	
    */
}


return $form;
