Ext.onReady(function() {
    MODx.load({ 
		xtype: 'xdbedit-page-home'
        ,object_id: Xdbedit.request.object_id
		,configs: Xdbedit.request.configs
    });
});

Xdbedit.page.Object = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        formpanel: 'xdbedit-panel-object'
        ,buttons: [{
            text: _('save')
            ,id: 'xdbedit-btn-save'
            ,process: 'mgr/xdbedit/update'
            ,method: 'remote'
            ,keys: [{
                key: 's'
                ,alt: true
                ,ctrl: true
            }]
        }]
		,components: [{
            xtype: 'xdbedit-panel-object'
            ,renderTo: 'xdbedit-panel-object-div'
            ,object_id: config.object_id
			,configs: config.configs
	        ,url: Xdbedit.config.connector_url
        },{
            xtype: 'xdbedit-grid-objects'
            ,preventRender: true
			,id: 'xdbedit-grid-objects'
			,configs: config.configs
        }]

    }); 
    Xdbedit.page.Object.superclass.constructor.call(this,config);
};
Ext.extend(Xdbedit.page.Object,MODx.Component);
Ext.reg('xdbedit-page-home',Xdbedit.page.Object);