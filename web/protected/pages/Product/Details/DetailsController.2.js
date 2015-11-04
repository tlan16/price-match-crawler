var PageJs=new Class.create;
PageJs.prototype=Object.extend(new DetailsPageJs,{load:function(){var a,b,c;a=this;a._init();$(a.getHTMLID("itemDiv")).addClassName("row");b=!0===a._item.sellInAllStores;c=(new Element("div",{"class":"row"})).insert({bottom:(new Element("div",{"class":"col-xs-6"})).update("Selling in Stores:")}).insert({bottom:(new Element("div",{"class":"col-xs-6 text-right"})).insert({bottom:(new Element("label")).update(" selling in all stores").insert({top:(new Element("input",{type:"checkbox","save-item":"allStores",
dirty:!1,checked:b})).observe("change",function(){this.writeAttribute("dirty",!0);a._refreshDirty()._getSaveBtn();!0===this.checked?$(this).up(".form-group").down(".form-control").hide():$(this).up(".form-group").down(".form-control").show()})})})});a._getInputDiv("name",a._item.name||"",$(a._containerIds.name),null,!0)._getInputDiv("description",a._item.description||"",$(a._containerIds.description))._getInputDiv("unitPrice",a.getCurrency(a._item.unitPrice||0,""),$(a._containerIds.unitPrice),"Unit Price ($)",
!0,void 0,!0)._getInputDiv("barcode",a._item.barcode||"",$(a._containerIds.barcode),"Barcode",!0)._getInputDiv("size",a._item.size||"",$(a._containerIds.size))._getInputDiv("labelVersionNo",a._item.labelVersionNo||"",$(a._containerIds.labelVersion),"Label Version",!0)._getInputDiv("useByVariance",a._item.usedByVariance||"",$(a._containerIds.useByVariance),"Days By From Printed",!0)._getSelect2Div("Material","materials",a._item.id?a._item.materials:[],$(a._containerIds.materials),null)._getSelect2Div("Category",
"categories",a._item.id?a._item.categories:[],$(a._containerIds.categories),null)._getSelect2Div("Store","stores",a._item.id?a._item.stores:[],$(a._containerIds.stores),c)._getSaveBtn();!0===b&&$(a._containerIds.stores).down(".form-control").hide();a._addNewComboBtn($(a._containerIds.materials),"material");a._item.materials&&Array.isArray(a._item.materials)&&a._item.materials.each(function(b){a._addComboRow(b,$(a._containerIds.materials),"material")});return a},_addNewComboBtn:function(a,b){var c,
d,e,f,g;c=this;d=a||null;e="New "+(b?c.ucfirst(b):"Info");if(!d||!d.id)return c;f=(new Element("button",{"class":"newStoreBtn btn btn-primary btn-sm"})).update(e).observe("click",function(a){f.writeAttribute("disabled",!0);d.insert({bottom:g=new Element("div")});c._signRandID(g);c._addComboRow(null,g);f.writeAttribute("disabled",!1)});d.update(c._getFormGroup("",f).addClassName("col-xs-12"));return c},_getComboRowDeleteBtn:function(a,b,c){var d,e,f,g,h;d=this;e=a||null;a=a?!0===a.active:!0;b=b||"";
f=c||"Info";return g=(new Element("button",{"class":!0===a?"btn btn-sm btn-danger":"btn btn-sm btn-success"})).addClassName(b).setStyle("margin-bottom: 15px;").insert({bottom:new Element("i",{"class":!0===a?"glyphicon glyphicon-trash":"glyphicon glyphicon-repeat"})}).observe("click",function(a){confirm("This "+(e?"":"newly added ")+f+" will be REMOVED, continue?")&&(h=g.up(".combo"),e&&e.id&&h.up().insert({bottom:new Element("input",{type:"hidden","save-item":"ignore_"+e.id,dirty:!0})}),h.remove(),
d._refreshDirty()._getSaveBtn())})},_addComboRow:function(a,b,c){var d,e;a=a||null;b=b||null;if(!b||!b.id)return this;b.insert({bottom:(new Element("div",{"class":"combo col-xs-12",combo_id:a?a.id:"new",active:a?a.active:!0})).insert({bottom:(new Element("div",{"class":"row "})).insert({bottom:b=new Element("div",{"class":"store col-md-7 col-sm-6 col-xs-12"})}).insert({bottom:d=new Element("div",{"class":"role col-md-4 col-sm-5  col-xs-12"})}).insert({bottom:(new Element("div",{"class":"pull-right text-right col-md-1 col-sm-1 col-xs-12"})).update(this._getComboRowDeleteBtn(a,
"col-xs-12",c))})})});this._getSelect2Div("Material","material",a&&a.material?{id:a.material.id,text:a.material.name,data:a.material}:null,b," ",!0,{multiple:!1,width:"100%",ajax:{delay:250,url:"/ajax/getAll",type:"GET",data:function(a){return{searchTxt:"name like ?",searchParams:["%"+a+"%"],entityName:"Material",pageNo:1}},results:function(a,b,c){e=[];a.resultData&&a.resultData.items&&a.resultData.items.each(function(a){e.push({id:a.id,text:a.name,data:a})});return{results:e}}},cache:!0,escapeMarkup:function(a){return a}})._getInputDiv("qty",
a&&a.material?a.qty:1,d," ",!1,"",!1,"Qty");return this},collectData:function(){var a,b,c;a=this;b=a._collectFormData($(a.getHTMLID("itemDiv")),"save-item");if(!b)return null;b.materials=[];$(a.getHTMLID("itemDiv")).getElementsBySelector(".combo[combo_id]").each(function(d){(c=a._collectFormData($(d),"save-item"))&&b.materials.push(c)});return b}});