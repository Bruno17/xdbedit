Xdbedit.panel.Object = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        id: 'xdbedit-panel-object'
		,title: _('template_variables')
        ,url: config.url
        ,baseParams: {configs: config.configs}		
        ,class_key: ''
        ,bodyStyle: 'padding: 15px;'
        ,autoHeight: true
        ,autoLoad: this.autoload(config)
        ,width: '97%'
        ,listeners: {
            'beforeSubmit': {fn:this.beforeSubmit,scope:this}
            ,'success': {fn:this.success,scope:this}
			,'load': {fn:this.load,scope:this}
        }		
    });
    Xdbedit.panel.Object.superclass.constructor.call(this,config);
	//this.addEvents({ load: true });
};
Ext.extend(Xdbedit.panel.Object,MODx.FormPanel,{
    autoload: function(config) {
		var a = {
            //url: MODx.config.manager_url+'index.php?a='+MODx.action['resource/tvs']
            url: config.url
			,method: 'GET'
            ,params: {
               //'a': MODx.action['resource/tvs']
                action: 'mgr/xdbedit/fields'
                ,object_id: config.object_id
				,configs: config.configs			   
               ,'class_key': 'modDocument'//config.class_key
            }
            ,scripts: true
            ,callback: function() {
                this.fireEvent('load');
                MODx.fireEvent('ready');
            }
            ,scope: this
        };
        return a;        	
    }
    
    ,
    setup: function() {

    }
    ,beforeSubmit: function(o) {
        tinyMCE.triggerSave(); 
    }
    ,success: function(o) {
		this.doAutoLoad();
		var gf = Ext.getCmp('xdbedit-grid-objects');
		gf.isModified = true;
		gf.refresh();
     },
	 load: function() {
        //console.log('test');
		MODx.loadRTE();
	  
	 }
		
    
});
Ext.reg('xdbedit-panel-object',Xdbedit.panel.Object);

MODx.fireResourceFormChange = function(f,nv,ov) {
    //Ext.getCmp('modx-panel-resource').fireEvent('fieldChange');
};