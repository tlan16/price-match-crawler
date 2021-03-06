/**
 * The DetailsPageJs file
 */
var DetailsPageJs = new Class.create();
DetailsPageJs.prototype = Object.extend(new BPCPageJs(), {
	_item: null //the item we are dealing with
	,_readOnlyMode: false
	,_dirty: false
	/**
	 * Getting a form group for forms
	 */
	,_getFormGroup: function (label, input, noFormControl) {
		var tmp = {};
		tmp.element = new Element('div', {'class': 'form-group form-group-sm'});
		if(label) {
			if((typeof(label) !== 'string') || (typeof(label) === 'string' && !label.blank()))
				tmp.element.insert({'bottom': new Element('label').update(label) });
		}
		tmp.element.insert({'bottom': input.addClassName(noFormControl === true ? '' : 'form-control') });
		return tmp.element;
	}
	,refreshParentWindow: function() {
		var tmp = {};
		tmp.me = this;
		if(!parent.window)
			return;
		tmp.parentWindow = parent.window;
		tmp.row = $(tmp.parentWindow.document.body).down('#' + tmp.parentWindow.pageJs.resultDivId + ' .item_row[item_id=' + tmp.me._item.id + ']');
		if(tmp.row) {
			tmp.row.replace(tmp.parentWindow.pageJs._getResultRow(tmp.me._item));
			if(!tmp.row.hasClassName('success'))
				tmp.row.addClassName('success');
		} else if($(tmp.parentWindow.document.body).down('#' + tmp.parentWindow.pageJs.resultDivId + ' #item-list-body')) {
			$(tmp.parentWindow.document.body).down('#' + tmp.parentWindow.pageJs.resultDivId + ' #item-list-body').insert({'top': tmp.parentWindow.pageJs._getResultRow(tmp.me._item) });
			if(tmp.totalEl = $(tmp.parentWindow.document.body).down('#' + tmp.parentWindow.pageJs.totalNoOfItemsId))
				tmp.totalEl.update(parseInt(tmp.totalEl.innerHTML) + 1);
		}
		return tmp.me;
	}
	,_getSaveBtn:function() {
		var tmp = {};
		tmp.me = this;
		tmp.me._refreshDirty();
		if(!tmp.me._containerIds || !tmp.me._containerIds.saveBtn)
			return tmp.me;
		tmp.container = $(tmp.me._containerIds.saveBtn);
		if(!tmp.container)
			return tmp.me;
		tmp.save = new Element('i')
			.addClassName('btn btn-success btn-md')
			.update('Save')
			.observe('click',function(e){
				tmp.btn = $(this);
				tmp.data = tmp.me.collectData();
				if(tmp.btn.readAttribute('disabled') === true || tmp.btn.readAttribute('disabled') === 'disabled' || tmp.data === null)
					return tmp.me;
				if(tmp.data === null)
					return tmp.me;
				if(tmp.me._item && tmp.me._item.id)
					tmp.data.id = tmp.me._item.id;
				tmp.me.saveItem(tmp.input, tmp.data);
			});
		tmp.cancel = new Element('i')
			.addClassName('btn btn-default btn-md')
			.update('Cancel')
			.observe('click',function(e){
				tmp.me.closeFancyBox();
			});

		tmp.container.update('').addClassName('col-xs-12')
			.insert({'bottom': tmp.me._getFormGroup(tmp.title, tmp.save).addClassName('col-xs-6') })
			.insert({'bottom': tmp.me._getFormGroup(tmp.title, tmp.cancel).addClassName('pull-right col-xs-6') })
		;

		if(tmp.me._dirty === false)
			tmp.save.hide();
		return tmp.me;
	}
	,collectData: function() {
		return this._collectFormData($(this.getHTMLID('itemDiv')), 'save-item');
	}
	,closeFancyBox:function () {
		if(parent.jQuery && parent.jQuery.fancybox)
			parent.jQuery.fancybox.close();
		else location.reload();
		return this;
	}
	,_getDatePickerDiv:function(saveItem, value, container, title, required, format, className) {
		var tmp = {};
		tmp.me = this;
		tmp.title = (title || tmp.me.ucfirst(saveItem));
		tmp.required = (required === true);
		tmp.className = (className || 'col-xs-12');
		tmp.format = (format || 'DD/MM/YYYY');

		if(!container.id)
			tmp.me._signRandID(container);
		tmp.container = $(container.id);
		if(!tmp.container)
			return;
		tmp.input = new Element('input')
			.writeAttribute({
				'required': tmp.required
				,'save-item': saveItem
				,'dirty': false
			})
			.setValue(value || '')
			;

		tmp.container.update(tmp.me._getFormGroup(tmp.title, tmp.input).addClassName(tmp.className) );

		if(typeof jQuery(document).datetimepicker !== 'function')
			return tmp.me;

		tmp.me._signRandID(tmp.input);
		tmp.datepicker = jQuery('#'+tmp.input.id).datetimepicker({
			format: tmp.format
			,showClear: !required
		});
		tmp.datepicker.on('dp.change keyup',function(e){
			if(tmp.datepicker.data('DateTimePicker') && tmp.datepicker.data('DateTimePicker').date()) {
				tmp.newValue =tmp.datepicker.data('DateTimePicker').date().local().format('YYYY-MM-DDThh:mm:ss');
				if(saveItem.endsWith('from'))
					tmp.newValue.format('YYYY-MM-DDT00:00:00');
				if(saveItem.endsWith('to'))
					tmp.newValue.format('YYYY-MM-DDT23:59:59');
			}
			else tmp.newValue = '';

			tmp.input.writeAttribute('dirty', value !== tmp.newValue);
			tmp.me._refreshDirty()._getSaveBtn();
		});
		return tmp.me;
	}
	,__validateContainer: function(container) {
		var tmp = {};
		tmp.me = this;
		tmp.container = (container || null);
		if(!tmp.container)
			return null;
		if(!tmp.container.id)
			tmp.me._signRandID(tmp.container);
		if(!tmp.container.id || jQuery('#'+tmp.container.id).length === 0)
			return null;
		return tmp.container;
	}
	,_getInputDiv:function(saveItem, value, container, title, required, className, isCurrency, placeholder) {
		var tmp = {};
		tmp.me = this;
		tmp.container = tmp.me.__validateContainer(container);
		tmp.title = (title || tmp.me.ucfirst(saveItem));
		tmp.required = (required === true);
		tmp.className = (className || 'col-xs-12');
		tmp.isCurrency = (isCurrency === true);
		tmp.placeholder = (placeholder || '');

		if(!tmp.container)
			return tmp.me;

		tmp.input = new Element('input')
			.writeAttribute({
				'required': tmp.required
				,'save-item': saveItem
				,'placeholder': (tmp.placeholder !== '' ? tmp.placeholder : tmp.title)
				,'dirty': false
			})
			.setValue(value || '')
			.observe('change',function(e){
				if(tmp.isCurrency === true)
					tmp.input.setValue(tmp.me.getValueFromCurrency($F(tmp.input)));
			})
			.observe('keyup',function(e){
				tmp.input.writeAttribute('dirty', value !== (tmp.isCurrency === true ? tmp.me.getValueFromCurrency($F(tmp.input)) : $F(tmp.input) ) );
				tmp.me._refreshDirty()._getSaveBtn();
			});

		tmp.container.update(tmp.me._getFormGroup(tmp.title, tmp.input).addClassName(tmp.className) );

		return tmp.me;
	}
	,_getSelectDiv:function(saveItem, options, selectedValues, container, title, required, className) {
		var tmp = {};
		tmp.me = this;
		tmp.container = tmp.me.__validateContainer(container);
		tmp.title = (title || tmp.me.ucfirst(saveItem));
		tmp.required = (required === true);
		tmp.className = (className || 'col-xs-12');
		if(!tmp.container)
			return tmp.me;

		tmp.input = new Element('select')
			.writeAttribute({
				'required': tmp.required
				,'save-item': saveItem
				,'dirty': false
			});

		if(Array.isArray(selectedValues)) {
			tmp.input.writeAttribute({'multiple': true});
		}

		options.each(function(option) {
			tmp.input.insert({'bottom': tmp.option = new Element('option', {'value': option.id}).update(option.name) });
			if((Array.isArray(selectedValues) && selectedValues.indexOf(option.id) > -1) || (option.id == selectedValues)) {
				tmp.option.writeAttribute('selected', true);
			}
		});
		tmp.input.observe('keyup',function(e){
			tmp.input.writeAttribute('dirty', $F(tmp.input) );
			tmp.me._refreshDirty()._getSaveBtn();
		});
		tmp.container.update(tmp.me._getFormGroup(tmp.title, tmp.input).addClassName(tmp.className) );
		return tmp.me;
	}
	,_refreshDirty: function() {
		var tmp = {};
		tmp.me = this;

		tmp.dirty = false;
		$(tmp.me.getHTMLID('itemDiv')).getElementsBySelector('[save-item]').each(function(el){
			if(tmp.dirty === false && (el.readAttribute('dirty') === true || el.readAttribute('dirty') === 'true' || el.readAttribute('dirty') === 'dirty') )
				tmp.dirty = true;
		});

		tmp.me._dirty = tmp.dirty;
		return tmp.me;
	}
	,_getSelect2Div:function(searchEntityName, saveItem, value, container, title, required, select2Options, className) {
		var tmp = {};
		tmp.me = this;
		tmp.container = tmp.me.__validateContainer(container);
		tmp.title = (title || tmp.me.ucfirst(saveItem));
		tmp.required = (required === true);
		tmp.select2Options = (select2Options || null);
		tmp.className = (className || 'col-xs-12');

		if(!tmp.container)
			return tmp.me;

		tmp.select2 = new Element('input')
			.writeAttribute('required', tmp.required)
			.writeAttribute('placeholder', 'Please select a ' + searchEntityName)
			.writeAttribute('save-item', saveItem);
		tmp.input = tmp.select2;

		tmp.container.insert({'bottom': tmp.me._getFormGroup(tmp.title, tmp.select2).addClassName(tmp.className) });

		tmp.me._signRandID(tmp.select2);

		tmp.data = [];
		if(tmp.me._item && tmp.me._item.id) {
			if(Array.isArray(value)) {
				value.each(function(item){
					tmp.data.push({'id': item.id, 'text': item.name, 'data': item});
				});
			} else tmp.data = value;
		}

		tmp.selectBox = jQuery('#'+tmp.select2.id).select2(tmp.select2Options ? tmp.select2Options : {
			multiple: true,
			allowClear: true,
			width: "100%",
			ajax: {
				delay: 250
				,url: '/ajax/getAll'
				,type: 'GET'
				,data: function (params) {
					return {"searchTxt": 'name like ?', 'searchParams': ['%' + params + '%'], 'entityName': searchEntityName, 'pageNo': 1};
				}
				,results: function(data, page, query) {
					tmp.result = [];
					if(data.resultData && data.resultData.items) {
						data.resultData.items.each(function(item){
							tmp.result.push({'id': item.id, 'text': item.name, 'data': item});
						});
					}
					return { 'results' : tmp.result };
				}
			}
			,cache: true
			,escapeMarkup: function (markup) { return markup; } // let our custom formatter work
		});
		tmp.selectBox.on('change', function(){
			tmp.selectBox.attr('dirty', tmp.selectBox.val() !== tmp.me._getNamesString(value,'id',','));
			tmp.me._refreshDirty()._getSaveBtn();
		});

		if(tmp.data)
			tmp.selectBox.select2('data', tmp.data);
		tmp.input.addClassName('transfered-to-select2');
		return tmp.me;
	}

	,setItem: function(item) {
		this._item = item;
		return this;
	}
	,saveItem: function(btn, data, onSuccFunc) {
		var tmp = {};
		tmp.me = this;
		if(btn) {
			tmp.me._signRandID(btn);
			jQuery('#' + btn.id).prop('disabled',true);
		}
		tmp.me._disableAll($(tmp.me.getHTMLID('itemDiv')));
		tmp.me.postAjax(tmp.me.getCallbackId('saveItem'), data, {
			'onSuccess': function (sender, param) {
				try {
					tmp.result = tmp.me.getResp(param, false, true);
					if(!tmp.result || !tmp.result.item || !tmp.result.item.id)
						return;
					tmp.me._item = tmp.result.item;
					if(typeof(onSuccFunc) === 'function')
						onSuccFunc(tmp.result);
					tmp.me.closeFancyBox();
				} catch (e) {
					tmp.me.showModalBox('<strong class="text-danger">ERROR:</strong>', e);
					tmp.me._refreshDirty()._getSaveBtn();
				}
			}
			, 'onComplete': function() {
				if(btn)
					jQuery('#' + btn.id).prop('disabled',false);
				tmp.me.refreshParentWindow();
			}
		});
		return tmp.me;
	}

	,_init: function(){
		return this;
	}
	,setPreData: function(data) {
		if(data)
			this._preSetData = data;
		return this;
	}
	,bindAllEventNObjects: function() {
		return this;
	}
	,load: function () {
		var tmp = {};
		tmp.me = this;
		tmp.me._init();

		$(tmp.me.getHTMLID('itemDiv')).addClassName('row');
		tmp.me
			._getInputDiv('name', (tmp.me._item.name || ''), $(tmp.me._containerIds.name), null ,true)
			._getInputDiv('description', (tmp.me._item.description || ''), $(tmp.me._containerIds.description))
			._getSaveBtn()
		;
		return tmp.me;
	}
	,_getCommentsDiv: function() {
		var tmp = {};
		tmp.me = this;

		tmp.container = $(tmp.me._containerIds.comments);

		tmp.comments = new Element('div');

		tmp.container.insert({'bottom': tmp.me._getFormGroup('Comments', tmp.comments, true).addClassName('col-md-12') });

		tmp.me._signRandID(tmp.comments);

		new CommentsDivJs(tmp.me, tmp.me._focusEntity, tmp.me._item.id)._setDisplayDivId(tmp.comments.id).render();

		return tmp.me;
	}
});