var PageJs=new Class.create;
PageJs.prototype=Object.extend(new BPCPageJs,{load:function(){this.getStoreList($(this._preData.containerId),this._preData.stores);return this},getStoreList:function(d,k){var a,e,g,h,f,b;a=this;e=d||null;g=k||[];if(!e)return a;e.insert({bottom:h=(new Element("div")).addClassName("list-group")});g.each(function(c){h.insert({bottom:(new Element("a",{href:"javascript:void(0)","class":"list-group-item",store_id:c.id})).addClassName(c.selected&&!0===c.selected?"active":"").update(c.name).observe("click",function(c){f=
$(this);if(f.hasClassName("active"))return a;b=f.readAttribute("store_id");if(!b||""===b||0==b)return a;a.switchStore(b)})})})},switchStore:function(d){this._disableAll();this.postAjax(this.getCallbackId("switchStore"),{store:d},{onComplete:function(){location.reload(!0)}});return this}});