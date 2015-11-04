/**
 * The page Js file
 */
var PageJs = new Class.create();
PageJs.prototype = Object.extend(new BPCPageJs(), {
	init: function() {
		var tmp = {};
		tmp.me = this;
		
		$('help-container').insert({'bottom': tmp.me.getHelpDiv() })
		
		return tmp.me;
	}
	,getHelpDiv: function() {
		var tmp = {};
		tmp.me = this;
		tmp.newDiv = new Element('div');
		tmp.openDriver = ['Start', 'Control Panel', 'Hardware and Sound', 'Devices and Printers', 'Brother QL-700', '(right-click) Printing Preference'];
		tmp.openDriverString = tmp.openDriver.join('&nbsp;&nbsp;<i class="fa fa-arrow-right"></i>&nbsp;&nbsp;');
		tmp.newDiv
			.insert({'bottom': new Element('h2', {'id': 'windows-setting'}).update('Windows') 
				.insert({'bottom': new Element('h3', {'id': 'windows-driver-setting'}).update('Driver Setting<br/>') })
				.insert({'bottom': new Element('h4').update(tmp.openDriverString) })
				.insert({'bottom': new Element('img', {'src': '/media/windows-driver-setting.png'}) })
				
				.insert({'bottom': new Element('h3', {'id': 'windows-driver-setting'}).update('Browser Setting<br/>') })
				.insert({'bottom': new Element('img', {'src': '/media/windows-chrome-setting.1.png'}) })
				.insert({'bottom': new Element('i', {'class': 'fa fa-arrow-right'}).setStyle('margin: 0 10px;') })
				.insert({'bottom': new Element('img', {'src': '/media/windows-chrome-setting.2.png'}) })
			})
		;
		return tmp.newDiv;
	}
});