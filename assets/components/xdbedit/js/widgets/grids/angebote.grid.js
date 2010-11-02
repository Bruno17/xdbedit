

Xdbedit.grid.Object = function(config) {
    config = config || {};
    this.sm = new Ext.grid.CheckboxSelectionModel();
	Ext.applyIf(config,{
        url: Xdbedit.config.connector_url
        ,baseParams: { 
		    action: 'mgr/xdbedit/getList',
			configs: config.configs}
        ,fields: ['id','pagetitle','firma','createdon','published','deleted']
        ,paging: true
		,autosave: false
        ,remoteSort: true
        ,primaryKey: 'id'
		,isModified : false
        ,sm: this.sm		
        ,columns: [this.sm,{
            header: 'id'
            ,dataIndex: 'id'
            ,sortable: true
            ,width: 50
        },{
            header: 'Titel'
            ,dataIndex: 'pagetitle'
            ,sortable: true
            ,width: 200
        },{
            header: 'Firma'
            ,dataIndex: 'firma'
            ,sortable: true
            ,width: 300
        },{
            header: 'Erstellt am'
            ,dataIndex: 'createdon'
            ,sortable: true
            ,width: 200
        },{
            header: 'Ver&ouml;ffentlicht'
            ,dataIndex: 'published'
            ,sortable: true
            ,width: 200
        }]
		,tbar: [{
            text: _(Xdbedit.customconfigs.task+'.bulk_actions')||_('xdbedit.bulk_actions')
            ,menu: [{
                text:_(Xdbedit.customconfigs.task+'.publish_selected')||_('xdbedit.publish_selected') 
                ,handler: this.publishSelected
                ,scope: this
            },{
                text:_(Xdbedit.customconfigs.task+'.unpublish_selected')||_('xdbedit.unpublish_selected') 
                ,handler: this.unpublishSelected
                ,scope: this
            },{
                text:_(Xdbedit.customconfigs.task+'.delete_selected')||_('xdbedit.delete_selected') 
                ,handler: this.deleteSelected
                ,scope: this
            },'-',{
                text:_(Xdbedit.customconfigs.task+'.remove_selected')||_('xdbedit.remove_selected') 
                ,handler: this.removeSelected
                ,scope: this
            }]
        },{
            text: _('year')+':'
        },{
            xtype: 'xdbedit-combo-year'
            ,id: 'xdbedit-filter-year'
            ,itemId: 'year'
            ,value: 'alle'
            ,width: 120
            ,listeners: {
                'select': {fn: this.changeYear,scope:this}
            }
        },{
            text: _('month')+':'
        },{
            xtype: 'xdbedit-combo-month'
            ,id: 'xdbedit-filter-month'
            ,itemId: 'month'
            ,value: 'alle'
            ,width: 120
            ,listeners: {
                'select': {fn:this.changeMonth,scope:this}
            }
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
	this.getStore().on('load',this.onStoreLoad,this);
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
    },getSelectedAsList: function() {
        var sels = this.getSelectionModel().getSelections();
        if (sels.length <= 0) return false;

        var cs = '';
        for (var i=0;i<sels.length;i++) {
            cs += ','+sels[i].data.id;
        }
        cs = Ext.util.Format.substr(cs,1,cs.length-1);
        return cs;
    },publishSelected: function(btn,e) {
        var cs = this.getSelectedAsList();
        if (cs === false) return false;
        
        MODx.Ajax.request({
            url: this.config.url
            ,params: {
                action: 'mgr/'+Xdbedit.customconfigs.task+'/bulkupdate'
				,configs: this.config.configs
				,task: 'publish'
                ,objects: cs
            }
            ,listeners: {
                'success': {fn:function(r) {
                    this.getSelectionModel().clearSelections(true);
                    this.refresh();
                },scope:this}
            }
        });
        return true;
    },unpublishSelected: function(btn,e) {
        var cs = this.getSelectedAsList();
        if (cs === false) return false;
        
        MODx.Ajax.request({
            url: this.config.url
            ,params: {
                action: 'mgr/'+Xdbedit.customconfigs.task+'/bulkupdate'
				,configs: this.config.configs
				,task: 'unpublish'
                ,objects: cs
            }
            ,listeners: {
                'success': {fn:function(r) {
                    this.getSelectionModel().clearSelections(true);
                    this.refresh();
                },scope:this}
            }
        });
        return true;
    },changeYear: function(cb,nv,ov) {
        this.setFilterParams(cb.getValue(),'alle');
    }
    ,changeMonth: function(cb,nv,ov) {
        this.setFilterParams(null,cb.getValue());
    }
   ,onStoreLoad: function() {
		if (this.isModified){
		var tb = this.getTopToolbar();
        if (!tb) {return false;}

           ycb = tb.getComponent('year');
            if (ycb) {
                //mcb.store.baseParams['year'] = y;
                ycb.store.load({
                    callback: function() {
                        ycb.collapse();
                    }
                });
				

            }
            mcb = tb.getComponent('month');
            if (mcb) {
                //mcb.store.baseParams['year'] = y;
                mcb.store.load({
                    callback: function() {
                        mcb.collapse();
                    }
                });

            }
		}

            this.isModified=false;
        /*
		var s = this.getStore();
        if (s) {
            //if (y) {s.baseParams['year'] = y;}
            //if (m) {s.baseParams['month'] = m || 'alle';}
            //s.removeAll();
        }
        */
        //this.getBottomToolbar().changePage(1);
        //this.refresh();
    }
    ,setFilterParams: function(y,m) {
        var tb = this.getTopToolbar();
        if (!tb) {return false;}

        var mcb;
        if (y) {
            tb.getComponent('year').setValue(y);

            mcb = tb.getComponent('month');
            if (mcb) {
                mcb.store.baseParams['year'] = y;
                mcb.store.load({
                    callback: function() {
                        mcb.setValue(m || 'alle');
                    }
                });
            }
        } 

        var s = this.getStore();
        if (s) {
            if (y) {s.baseParams['year'] = y;}
            if (m) {s.baseParams['month'] = m || 'alle';}
            s.removeAll();
        }
        this.getBottomToolbar().changePage(1);
        this.refresh();
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
        //this.store.on('load', this.reloadDateCombos(this)); 
		//console.log(this.store);
		var n = this.menu.record; 
        //var cls = n.cls.split(',');
        var m = [];
        m.push({
            text: _(Xdbedit.customconfigs.task+'.edit')||_('xdbedit.edit')
            ,handler: this.editObject
        });
        m.push('-');
        m.push({
            text: _(Xdbedit.customconfigs.task+'.create')||_('xdbedit.create')
            ,handler: this.createObject
        });
        m.push('-');
        if (n.published == 0) {
            m.push({
                text: _(Xdbedit.customconfigs.task+'.publish')||_('xdbedit.publish')
                ,handler: this.publishObject
            })
        } else if (n.published == 1) {
            m.push({
                text:_(Xdbedit.customconfigs.task+'.unpublish')||_('xdbedit.unpublish')
                ,handler: this.unpublishObject
            });
        }		
        this.addContextMenuItem(m);
    }
});
Ext.reg('xdbedit-grid-objects',Xdbedit.grid.Object);

Xdbedit.combo.Month = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        name: 'month'
        ,hiddenName: 'month'
        ,forceSelection: true
        ,typeAhead: false
        ,editable: false
        ,allowBlank: false
        ,listWidth: 300		
		,resizable: false
        ,pageSize: 0		
        ,url: Xdbedit.config.connector_url
        ,fields: ['name']
        ,displayField: 'name'
        ,valueField: 'name'
        ,baseParams: {
		    action: 'mgr/'+Xdbedit.customconfigs.task+'/getdates',
			configs: Xdbedit.config.configs,
			mode: 'month',
			year: 'alle'
        }
    });
    Xdbedit.combo.Month.superclass.constructor.call(this,config);
};
Ext.extend(Xdbedit.combo.Month,MODx.combo.ComboBox);
Ext.reg('xdbedit-combo-month',Xdbedit.combo.Month);

Xdbedit.combo.Year = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        name: 'year'
        ,hiddenName: 'year'
        ,forceSelection: true
        ,typeAhead: false
        ,editable: false
        ,allowBlank: false
        ,listWidth: 300
		,resizable: false
        ,pageSize: 0
        ,url: Xdbedit.config.connector_url
        ,fields: ['name']
        ,displayField: 'name'
        ,valueField: 'name'
        ,baseParams: { 
		    action: 'mgr/'+Xdbedit.customconfigs.task+'/getdates',
			configs: Xdbedit.config.configs,
			mode: 'year'}			

    });
    Xdbedit.combo.Year.superclass.constructor.call(this,config);
};
Ext.extend(Xdbedit.combo.Year,MODx.combo.ComboBox);
Ext.reg('xdbedit-combo-year',Xdbedit.combo.Year);