var Xdbedit = function(config) {
    config = config || {};
    Xdbedit.superclass.constructor.call(this,config);
};
Ext.extend(Xdbedit,Ext.Component,{
    page:{},window:{},grid:{},tree:{},panel:{},combo:{},config: {}
});
Ext.reg('xdbedit',Xdbedit);

var Xdbedit = new Xdbedit();