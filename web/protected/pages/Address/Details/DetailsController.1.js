/**
 * The page Js file
 */
var PageJs = new Class.create();
PageJs.prototype = Object.extend(new DetailsPageJs(), {
	_readOnlyMode: false
	,_selectTypeTxt: 'Select One...'
	/**
	 * Set some pre defined data before javascript start
	 */
	,setPreData: function() {
		return this;
	}
	,_getCommentsDiv() {
		var tmp = {};
		tmp.me = this;

		tmp.container = $(tmp.me._containerIds.comments);
		
		tmp.comments = new Element('div');
		
		tmp.container.insert({'bottom': tmp.me._getFormGroup('Comments', tmp.comments, true).addClassName('col-md-12') });
		
		tmp.me._signRandID(tmp.comments);
		
		new CommentsDivJs(tmp.me, 'Question', tmp.me._item.id)._setDisplayDivId(tmp.comments.id).render();
		
		return tmp.me;
	}
	,load: function () {
		var tmp = {};
		tmp.me = this;
		tmp.me._init();
		
		$(tmp.me.getHTMLID('itemDiv')).addClassName('row');
		tmp.me
			._getInputDiv('contactName', (tmp.me._item.contactName || ''), $(tmp.me._containerIds.contactName))
			._getInputDiv('contactNo', (tmp.me._item.contactNo || ''), $(tmp.me._containerIds.contactNo))
			._getInputDiv('street', (tmp.me._item.street || ''), $(tmp.me._containerIds.street))
			._getInputDiv('city', (tmp.me._item.city || ''), $(tmp.me._containerIds.city))
			._getInputDiv('region', (tmp.me._item.region || ''), $(tmp.me._containerIds.region))
			._getInputDiv('country', (tmp.me._item.country || ''), $(tmp.me._containerIds.country))
			._getInputDiv('postCode', (tmp.me._item.postCode || ''), $(tmp.me._containerIds.postCode))
			._getSaveBtn()
		;
		return tmp.me;
	}
	/**
	 * Public: binding all the js events
	 */
	,bindAllEventNObjects: function() {
		var tmp = {};
		tmp.me = this;
		return tmp.me;
	}
});