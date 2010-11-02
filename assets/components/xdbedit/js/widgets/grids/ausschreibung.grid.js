

Xdbedit.grid.Object = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        url: Xdbedit.config.connector_url
        ,baseParams: { 
		    action: 'mgr/xdbedit/getList',
			configs: config.configs}
        ,fields: ['id','nr','rennklasse','altersklasse','bootsklasse']
        ,paging: true
		,autosave: false
        ,remoteSort: true
        ,primaryKey: 'id'
        ,columns: [{
            header: 'id'
            ,dataIndex: 'id'
            ,sortable: true
            ,width: 10
        },{
            header: 'Nummer'
            ,dataIndex: 'nr'
            ,sortable: true
            ,width: 80
        },{
            header: 'Rennklasse'
            ,dataIndex: 'rennklasse'
            ,sortable: true
            ,width: 80
        },{
            header: 'Altersklasse'
            ,dataIndex: 'altersklasse'
            ,sortable: true
            ,width: 80
        },{
            header: 'Bootsklasse'
            ,dataIndex: 'bootsklasse'
            ,sortable: true
            ,width: 40
        }]        
		,viewConfig: {
            forceFit:true,
            //enableRowBody:true,
            //showPreview:true,
            getRowClass : function(rec, ri, p){
                var cls = 'xdbedit-object';
                if (!rec.data.published) cls += ' xdbedit-unpublished';
                if (rec.data.deleted) cls += ' xdbedit-deleted';

                return cls;
            }
        }
    });
    Xdbedit.grid.Object.superclass.constructor.call(this,config)
};
Ext.extend(Xdbedit.grid.Object,MODx.grid.Grid,{
    _renderUrl: function(v,md,rec) {
        return '<a href="'+v+'" target="_blank">'+rec.data.pagetitle+'</a>';
    }
    ,editObject: function() {
		formpanel=Ext.getCmp('xdbedit-panel-object');
        formpanel.autoLoad.params.object_id=this.menu.record.id;
		formpanel.doAutoLoad();
		
		//location.href = '?a='+MODx.request.a+'&action=editorpage&object_id='+this.menu.record.id;
    }
    ,createObject: function() {
		formpanel=Ext.getCmp('xdbedit-panel-object');
        formpanel.autoLoad.params.object_id='neu';
		formpanel.doAutoLoad();		
        //location.href = '?a='+MODx.request.a+'&action=editorpage&object_id=neu';
    }
	,publishObject: function() {
        MODx.Ajax.request({
            url: this.config.url
            ,params: {
                action: 'mgr/xdbedit/update'
				,task: 'publish'
                ,object_id: this.menu.record.id
				,configs: this.config.configs
            }
            ,listeners: {
                'success': {fn:this.refresh,scope:this}
            }
        });
    }
	,unpublishObject: function() {
        MODx.Ajax.request({
            url: this.config.url
            ,params: {
                action: 'mgr/xdbedit/update'
				,task: 'unpublish'
                ,object_id: this.menu.record.id
				,configs: this.config.configs
            }
            ,listeners: {
                'success': {fn:this.refresh,scope:this}
            }
        });
    }	
    ,truncateThread: function() {        
        MODx.msg.confirm({
            title: _('warning')
            ,text: _('boerse.thread_truncate_confirm')
            ,url: this.config.url
            ,params: {
                action: 'mgr/thread/truncate'
                ,thread: this.menu.record.name
            }
            ,listeners: {
                'success': {fn:this.refresh,scope:this}
            }
        });
    }
    ,getMenu: function() {
        var n = this.menu.record; 
        //var cls = n.cls.split(',');
        var m = [];
        m.push({
            text: 'Angebot bearbeiten'
            ,handler: this.editObject
        });
        m.push('-');
        m.push({
            text: 'Angebote erstellen'
            ,handler: this.createObject
        });
        m.push('-');
        if (n.published == 0) {
            m.push({
                text: 'ver&ouml;ffentlichen'
                ,handler: this.publishObject
            })
        } else if (n.published == 1) {
            m.push({
                text:'zur&uuml;ckziehen'
                ,handler: this.unpublishObject
            });
        }		
        this.addContextMenuItem(m);
    }
});
Ext.reg('xdbedit-grid-objects',Xdbedit.grid.Object);