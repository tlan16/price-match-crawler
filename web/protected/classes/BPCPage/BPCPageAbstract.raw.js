/**
 * The FrontEndPageAbstract Js file
 */
var BPCPageJs = new Class.create();
BPCPageJs.prototype = {
	modalId: 'page_modal_box_id'
	,_htmlIDs: {}

	,_ajaxRequest: null

	//the callback ids
	,callbackIds: {}

	//constructor
	,initialize: function () {}

	,setCallbackId: function(key, callbackid) {
		this.callbackIds[key] = callbackid;
		return this;
	}

	,getCallbackId: function(key) {
		if(this.callbackIds[key] === undefined || this.callbackIds[key] === null)
			throw 'Callback ID is not set for:' + key;
		return this.callbackIds[key];
	}


	,setHTMLID: function($key, $value) {
		this._htmlIDs[$key]  = $value;
		return this;
	}

	,getHTMLID: function($key) {
		return this._htmlIDs[$key];
	}

	,getFormGroup: function(label, input, _wantInpuClass) {
		var tmp = {};
		tmp.me = this;
		tmp.withFormControlClass = (_wantInpuClass || false);
		tmp.newDiv = new Element('div').addClassName('form-group');
		if(label)
			tmp.newDiv.insert({'bottom': label.addClassName('control-label')});
		if(input) {
			if(tmp.withFormControlClass === true)
				input.addClassName('form-control')
			tmp.newDiv.insert({'bottom':  input});
		}
		return tmp.newDiv;
	}

	//posting an ajax request
	,postAjax: function(callbackId, data, requestProperty, timeout) {
		var tmp = {};
		tmp.me = this;
		tmp.me._ajaxRequest = new Prado.CallbackRequest(callbackId, requestProperty);
		tmp.me._ajaxRequest.setCallbackParameter(data);
		tmp.timeout = (timeout || 30000);
		if(tmp.timeout < 30000) {
			tmp.timeout = 30000;
		}
		tmp.me._ajaxRequest.setRequestTimeOut(tmp.timeout);
		tmp.me._ajaxRequest.dispatch();
		return tmp.me._ajaxRequest;
	}

	,abortAjax: function() {
		if(this._ajaxRequest !== null)
			this._ajaxRequest.abort();
	}

	//parsing an ajax response
	,getResp: function (response, expectNonJSONResult, noAlert) {
		var tmp = {};
		tmp.expectNonJSONResult = (expectNonJSONResult !== true ? false : true);
		tmp.result = response;
		if(tmp.expectNonJSONResult === true)
			return tmp.result;
		if(!tmp.result || !tmp.result.isJSON()) {
			return;
//			tmp.error = 'Invalid JSON string: ' + tmp.result;
//			if (noAlert === true)
//				throw tmp.error;
//			else
//				return alert(tmp.error);
		}
		tmp.result = tmp.result.evalJSON();
		if(tmp.result.errors.size() !== 0) {
			tmp.error = 'Error: \n\n' + tmp.result.errors.join('\n');
			if (noAlert === true)
				throw tmp.error;
			else
				return alert(tmp.error);
		}
		return tmp.result.resultData;
	}
	//format the currency
	,getCurrency: function(number, dollar, decimal, decimalPoint, thousandPoint, format) {
		var tmp = {};
		tmp.decimal = (isNaN(decimal = Math.abs(decimal)) ? 2 : decimal);
		tmp.dollar = (dollar == undefined ? "$" : dollar);
		tmp.decimalPoint = (decimalPoint == undefined ? "." : decimalPoint);
		tmp.thousandPoint = (thousandPoint == undefined ? "," : thousandPoint);
		tmp.sign = (number < 0 ? "-" : "");
		tmp.format = (format || "%s%v");
		
		if(typeof accounting === 'object' && typeof accounting.formatMoney === 'function')
			return accounting.formatMoney(number,tmp.dollar,tmp.decimal,tmp.thousandPoint,tmp.decimalPoint,tmp.format);
		
		tmp.Int = parseInt(number = Math.abs(+number || 0).toFixed(tmp.decimal)) + "";
		tmp.j = (tmp.j = tmp.Int.length) > 3 ? tmp.j % 3 : 0;
		return tmp.dollar + tmp.sign + (tmp.j ? tmp.Int.substr(0, tmp.j) + tmp.thousandPoint : "") + tmp.Int.substr(tmp.j).replace(/(\d{3})(?=\d)/g, "$1" + tmp.thousandPoint) + (tmp.decimal ? tmp.decimalPoint + Math.abs(number - tmp.Int).toFixed(tmp.decimal).slice(2) : "");
	}
	/**
	 * Getting the absolute value from currency
	 */
	,getValueFromCurrency: function(currency, decimalSeparator) {
		var tmp = {};
		tmp.currency = (currency || '');
		tmp.decimalSeparator = (decimalSeparator || '.');
		
		if(tmp.currency === '')
			return tmp.currency;
		
		if(typeof accounting === 'object' && typeof accounting.unformat === 'function')
			return accounting.unformat(tmp.currency, tmp.decimalSeparator);
		
		tmp.reg = /^-?\d*[\.]?\d+$/;
		tmp.result =  (tmp.currency + '').replace(/\s*/g, '').replace(/\$/g, '').replace(/,/g, '');
		tmp.result = tmp.reg.exec(tmp.result);
		return tmp.result;
	}
	//do key enter
	,keydown: function (event, enterFunc, nFunc, keyValue) {
		var tmp = {};
		tmp.keyValue = keyValue ? keyValue : 13;
		//if it's not a enter key, then return true;
		if(!((event.which && event.which == tmp.keyValue) || (event.keyCode && event.keyCode == tmp.keyValue))) {
			if(typeof(nFunc) === 'function') {
				nFunc();
			}
			return true;
		}

		if(typeof(enterFunc) === 'function') {
			enterFunc();
		}
		return false;
	}
	//getting the error message box
	,getAlertBox: function(title, msg) {
		return new Element('div', {'class': 'alert alert-dismissible', 'role': 'alert'})
		.insert({'bottom': new Element('button', {'class': '', 'data-dismiss': 'alert'})
			.insert({'bottom': new Element('span', {'aria-hidden': 'true'}).update('&times;') })
			.insert({'bottom': new Element('span', {'class': 'sr-only'}).update('Close') })
		})
		.insert({'bottom': new Element('strong').update(title) })
		.insert({'bottom': msg });
	}
	/**
	 * give the input box a random id
	 */
	,_signRandID: function(input) {
		if(!input)
			return this;
		if(!input.id)
			input.id = 'input_' + String.fromCharCode(65 + Math.floor(Math.random() * 26)) + Date.now();
		return this;
	}
	/**
	 * Marking a form group to has-error
	 */
	,_markFormGroupError: function(input, errMsg) {
		var tmp = {};
		tmp.me = this;
		tmp.visible = input.visible();
		if(input.up('.form-group')) {
			input.store('clearErrFunc', function(btn) {
				input.up('.form-group').removeClassName('has-error');
				jQuery('#' + input.id).tooltip('hide').tooltip('destroy').show();
			})
			.up('.form-group').addClassName('has-error');
			tmp.me._signRandID(input);
			jQuery('#' + input.id).tooltip({
				'trigger': 'manual'
				,'placement': 'auto'
				,'container': 'body'
				,'placement': 'bottom'
				,'html': true
				,'title': errMsg
				,'content': errMsg
			})
			.tooltip('show');
			jQuery('#' + input.id).on('keyup change dp.change', function(){
				tmp.func = $(input).retrieve('clearErrFunc');
				if(typeof(tmp.func) === 'function')
					tmp.func();
			});
			if(!tmp.visible) {
				input.hide();
			}
		}
		return tmp.me;
	}
	/**
	 * Collecting data from attrName
	 */
	,_collectFormData: function(container, attrName, groupIndexName, ignoreError) {
		var tmp = {};
		tmp.me = this;
		tmp.data = {};
		tmp.hasError = false;
		tmp.ignoreError = (ignoreError === true ? true : false);
		$(container).getElementsBySelector('[' + attrName + ']').each(function(item) {
			tmp.groupIndexName = groupIndexName ? item.readAttribute(groupIndexName) : null;
			tmp.fieldName = item.readAttribute(attrName);
			if(tmp.ignoreError !== true && item.hasAttribute('required') && $F(item).blank()) {
				tmp.me._markFormGroupError(item, 'This is requried');
				tmp.hasError = true;
			}

			tmp.itemValue = item.readAttribute('type') !== 'checkbox' ? $F(item) : $(item).checked;
			if(item.hasAttribute('validate_currency') || item.hasAttribute('validate_number')) {
				if (tmp.ignoreError !== true && tmp.me.getValueFromCurrency(tmp.itemValue).match(/^(-)?\d+(\.\d{1,4})?$/) === null) {
					tmp.me._markFormGroupError(item, (item.hasAttribute('validate_currency') ? item.readAttribute('validate_currency') : item.hasAttribute('validate_number')));
					tmp.hasError = true;
				}
				tmp.value = tmp.me.getValueFromCurrency(tmp.itemValue);
			}

			//getting the data
			if(tmp.groupIndexName !== null && tmp.groupIndexName !== undefined) {
				if(!tmp.data[tmp.groupIndexName])
					tmp.data[tmp.groupIndexName] = {};
				tmp.data[tmp.groupIndexName][tmp.fieldName] = tmp.itemValue;
			} else {
				tmp.data[tmp.fieldName] = tmp.itemValue;
			}
		});
		return (tmp.hasError === true ? null : tmp.data);
	}

	,showModalBox: function(title, content, isSM, footer, eventFuncs, noClose) {
		var tmp = {};
		tmp.me = this;
		tmp.isSM = (isSM === true ? true : false);
		tmp.noClose = (noClose === true ? true : false);
		tmp.footer = (footer || null);
		if(!$(tmp.me.modalId)) {
			tmp.newBox = new Element('div', {'id': tmp.me.modalId, 'class': 'modal', 'tabindex': '-1', 'role': 'dialog', 'aria-hidden': 'true', 'aria-labelledby': 'page-modal-box'})
				.insert({'bottom': new Element('div', {'class': 'modal-dialog ' + (tmp.isSM === true ? 'modal-sm' : 'modal-lg') })
					.insert({'bottom': new Element('div', {'class': 'modal-content' })
						.insert({'bottom': new Element('div', {'class': 'modal-header' })
							.insert({'bottom': new Element('div', {'class': 'close', 'type': 'button', 'data-dismiss': 'modal'})
								.insert({'bottom':new Element('span', {'aria-hidden': 'true'}).update('&times;') })
							})
							.insert({'bottom': new Element('strong', {'class': 'modal-title'}).update(title) })
						})
						.insert({'bottom': new Element('div', {'class': 'modal-body' }).update(content) })
						.insert({'bottom': tmp.footer === null ? '' : new Element('div', {'class': 'modal-footer' }).update(tmp.footer) })
					})
				});
			$$('body')[0].insert({'bottom': tmp.newBox});
			tmp.modal = jQuery('#' + tmp.me.modalId);
			if(tmp.noClose === true) {
				tmp.modal.modal({
					backdrop: 'static',
					keyboard: false
				});
			}
			if(eventFuncs && typeof(eventFuncs) === 'object') {
				$H(eventFuncs).each(function(eventFunc){
					tmp.modal.on(eventFunc.key, eventFunc.value);
				});
			}
		} else {
			tmp.modal = jQuery('#' + tmp.me.modalId);
			tmp.dialogDiv = tmp.modal.find('.modal-dialog').removeClass('modal-sm').removeClass('modal-lg').addClass(tmp.isSM === true ? 'modal-sm' : 'modal-lg');
			tmp.modal.find('.modal-title').html(title);
			tmp.modal.find('.modal-body').html(content);
			if(tmp.modal.find('.modal-footer').length > 0) {
				if(tmp.footer !== null)
					tmp.modal.find('.modal-footer').html(tmp.footer);
				else
					tmp.modal.find('.modal-footer').remove();
			} else {
				if(tmp.footer !== null)
					jQuery('<div class="modal-footer"></div>').html(tmp.footer).appendTo(tmp.dialogDiv.find('.modal-content'));
			}
		}

		if(!tmp.modal.hasClass('in'))
			tmp.modal.modal().show();
		return tmp.me;
	}
	,hideModalBox: function() {
		jQuery('#' + this.modalId).modal('hide');
	}
	/**
	 * returning a loading image
	 */
	,getLoadingImg: function() {
		return new Element('span', {'class': 'loading-img fa fa-refresh fa-5x fa-spin'});
	}
	,removeLoadingImg: function() {
		jQuery('.loading-img').remove();
	}
	/**
	 * Load the mysql utc time into Date object
	 */
	,loadUTCTime: function (utcString) {
		var tmp = {};
		tmp.strings = utcString.strip().split(' ');
		tmp.dateStrings = tmp.strings[0].split('-');
		tmp.timeStrings = tmp.strings[1].split(':');
		return new Date(Date.UTC(tmp.dateStrings[0], (tmp.dateStrings[1] * 1 - 1), tmp.dateStrings[2], tmp.timeStrings[0], tmp.timeStrings[1], tmp.timeStrings[2]));
	}
	/**
	 * double click n dblclick
	 */
	,observeClickNDbClick: function(element, clickFunc, dblClickFunc) {
		var tmp = {};
		tmp.me = this;
		$(element).observe('click', function(event){
			if($(element).retrieve('alreadyclicked') === true) {
				$(element).store('alreadyclicked', false);
				if ($(element).retrieve('alreadyclickedTimeout')){
					clearTimeout($(element).retrieve('alreadyclickedTimeout'));
				}
				if(typeof dblClickFunc === 'function') {
					dblClickFunc(event);
				}
			} else {
				$(element).store('alreadyclicked', true);
				$(element).store('alreadyclickedTimeout', setTimeout(function(){
					$(element).store('alreadyclicked', false); // reset when it happens
					if(typeof clickFunc === 'function') {
						clickFunc(event);
					}
		        },300));
			}
		});
		return tmp.me;
	}
	/**
	 * geting params from url
	 */
	,getUrlParam: function (name) {
		var tmp = {};
		tmp.me = this;
	    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
	    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
	        results = regex.exec(location.search);
	    return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
	}
	/**
	 * open url in new tab
	 */
	,openInNewTab: function(url) {
		window.open(url, '_blank').focus();
		return this;
	}
	/**
	 * pause javascript for given time
	 */
	,sleep: function(sleepDuration, func) {
		var tmp = {};
		tmp.now = new Date().getTime();
		while (new Date().getTime() < tmp.now + sleepDuration){ 
			if(typeof func === 'function')
				func(new Date().getTime());
		}
		return this;
	}
	/**
	 * upper case first character
	 */
	,ucfirst: function(string) {
		if(string.length === 0)
			return string;
		return string.charAt(0).toUpperCase() + string.slice(1);
	}
	/**
	 * convert prototype element to jQuery element
	 */
	,_elTojQuery: function (el) {
		var tmp = {};
		tmp.me = this;
		tmp.el = (el || null);
		if(tmp.el === null)
			return null;
		tmp.me._signRandID(tmp.el);
		tmp.el = jQuery('#'+tmp.el.id);
		return tmp.el;
	}
	/**
	 * join custom attribute in array elements with custom glue
	 */
	,_getNamesString: function(objs, name, glue) {
		var tmp = {};
		tmp.me = this;
		tmp.name = (name || 'name');
		tmp.glue = (glue || ', ');
		tmp.result = "";
		if(!Array.isArray(objs)) {
			if(objs !== null && typeof objs === 'object' && typeof objs[tmp.name] !== 'undefined')
				return objs[tmp.name];
			else return tmp.result;
		}
		tmp.names = [];
		objs.each(function(obj){
			if(obj !== null && typeof obj[tmp.name] !== 'undefined')
				tmp.names.push(obj[tmp.name]);
		});
		tmp.result = tmp.names.join(tmp.glue);
		return tmp.result;
	}
	/**
	 * disable all buttons, input fields etc.. 
	 */
	,_disableAll: function(container, selector) {
		var tmp = {};
		tmp.me = this;
		tmp.selector = (selector || 'button,.btn,input,[save-item],[search_field],select,.select2');
		tmp.container = (container || null);
		if(tmp.container)
			tmp.container = tmp.me._elTojQuery(tmp.container);
		else tmp.container = jQuery(document);
		if(typeof tmp.selector === 'string' && tmp.selector.trim() !== '')
			tmp.container.find(tmp.selector).prop('disabled', true).attr('disabled', true);
	}
};
