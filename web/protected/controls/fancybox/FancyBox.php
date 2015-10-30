<?php
/**
 * The SocialBtns Loader
 *
 * @package    web
 * @subpackage controls
 * @author     lhe<helin16@gmail.com>
 */
class FancyBox extends TClientScript
{
	/**
	 * (non-PHPdoc)
	 * @see TControl::onLoad()
	 */
	public function onLoad($param)
	{
		$clientScript = $this->getPage()->getClientScript();
		if(!$this->getPage()->IsPostBack || !$this->getPage()->IsCallback)
		{
			// Add jQuery library
			// Add mousewheel plugin (this is optional)
			$clientScript->registerHeadScriptFile('jquery.mousewheel', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-mousewheel/3.0.6/jquery.mousewheel.min.js');
			// Add fancyBox main JS and CSS files
			$clientScript->registerHeadScriptFile('jquery.fancybox', 'https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.min.js');
			$clientScript->registerStyleSheetFile('jquery.fancybox.css', 'https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.min.css', 'screen');
			
			// Add fancyBox Button helper
			$clientScript->registerStyleSheetFile('jquery.fancybox.btn.css', 'https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/helpers/jquery.fancybox-buttons.css');
			$clientScript->registerHeadScriptFile('jquery.fancybox.btn', 'https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/helpers/jquery.fancybox-buttons.js');
			// Add fancyBox Thumbnail helper (this is optional)
			$clientScript->registerStyleSheetFile('jquery.fancybox.thumb.css', 'https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/helpers/jquery.fancybox-thumbs.css', 'screen');
			$clientScript->registerHeadScriptFile('jquery.fancybox.thumb', 'https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/helpers/jquery.fancybox-thumbs.js');
			// Add fancyBox Media helper (this is optional) -->
			$clientScript->registerHeadScriptFile('jquery.fancybox.media', 'https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/helpers/jquery.fancybox-media.js');
			
			$clientScript->registerBeginScript('jquery.noConflict', 'jQuery.noConflict();');
		}
	}
}