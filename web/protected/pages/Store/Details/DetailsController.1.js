/**
 * The page Js file
 */
var PageJs = new Class.create();
PageJs.prototype = Object.extend(new DetailsPageJs(), {
	load: function () {
		var tmp = {};
		tmp.me = this;
		tmp.me._init();
		
		$(tmp.me.getHTMLID('itemDiv')).addClassName('row');
		tmp.me
			._getInputDiv('name', (tmp.me._item.name || ''), $(tmp.me._containerIds.name), null ,true)
			._getInputDiv('description', (tmp.me._item.description || ''), $(tmp.me._containerIds.description))
			._getInputDiv('contactName', (tmp.me._item.address ? tmp.me._item.address.contactName : ''), $(tmp.me._containerIds.contactName))
			._getInputDiv('contactNo', (tmp.me._item.address ? tmp.me._item.address.contactNo : ''), $(tmp.me._containerIds.contactNo))
			._getInputDiv('street', (tmp.me._item.address ? tmp.me._item.address.street : ''), $(tmp.me._containerIds.street))
			._getInputDiv('city', (tmp.me._item.address ? tmp.me._item.address.city : ''), $(tmp.me._containerIds.city))
			._getInputDiv('region', (tmp.me._item.address ? tmp.me._item.address.region : ''), $(tmp.me._containerIds.region))
			._getInputDiv('country', (tmp.me._item.address ? tmp.me._item.address.country : ''), $(tmp.me._containerIds.country))
			._getInputDiv('postCode', (tmp.me._item.address ? tmp.me._item.address.postCode : ''), $(tmp.me._containerIds.postCode))
			._getSaveBtn()
		;
		return tmp.me;
	}
});