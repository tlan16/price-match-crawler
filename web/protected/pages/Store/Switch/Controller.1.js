/**
 * The page Js file
 */
var PageJs = new Class.create();
PageJs.prototype = Object.extend(new BPCPageJs(), {
	load: function() {
		var tmp = {};
		tmp.me = this;
		
		tmp.me.getStoreList($(tmp.me._preData.containerId), tmp.me._preData.stores);
		
		return tmp.me;
	}
	,getStoreList: function(container, stores) {
		var tmp = {};
		tmp.me = this;
		tmp.container = (container || null);
		tmp.stores = (stores || []);
		
		if(!tmp.container)
			return tmp.me;
		
		tmp.container.insert({'bottom': tmp.listGroup = new Element('div').addClassName('list-group') });
		
		tmp.stores.each(function(item){
			tmp.listGroup.insert({'bottom': new Element('a', {'href': 'javascript:void(0)', 'class': 'list-group-item', 'store_id': item.id})
				.addClassName((item.selected && item.selected === true) ? 'active' : '')
				.update(item.name)
				.observe('click', function(e){
					tmp.btn = $(this);
					if(tmp.btn.hasClassName('active'))
						return tmp.me;
					tmp.target = tmp.btn.readAttribute('store_id');
					if(!tmp.target || tmp.target === '' || tmp.target == 0)
						return tmp.me;
					tmp.me.switchStore(tmp.target);
				})
			});
		});
	}
	,switchStore: function(storeId) {
		var tmp = {};
		tmp.me = this;
		
		tmp.me._disableAll();
		tmp.me.postAjax(tmp.me.getCallbackId('switchStore'), {'store': storeId}, {
			'onComplete': function() {
//				location.reload(true); // force ignore browser cache
			}
		});
		return tmp.me;
	}
});