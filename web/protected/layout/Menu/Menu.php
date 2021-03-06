<?php
/**
 * Menu template
 *
 * @package    Web
 * @subpackage Layout
 * @author     lhe
 */
class Menu extends TTemplateControl
{
    /**
     * (non-PHPdoc)
     * @see TControl::onLoad()
     */
	public function onLoad($param)
	{
	}
	public function getMenuItems()
	{
		$pageItem = trim($this->getPage()->menuItem);
		$array = array(
			'' => array('url' => '/', 'name' => 'Home', 'icon' => '<span class="glyphicon glyphicon-home"></span>')
			,'product' => array(
				'name' => 'Products',
				'url' => '/products.html',
				'icon' => '<span class="glyphicon glyphicon-tag"></span>'
			)
			,'record' => array(
				'name' => 'Records',
				'url' => '/records.html',
				'icon' => '<span class="glyphicon glyphicon-eye-open"></span>'
			)
			,'Resources' => array(
				'icon' => '<span class="glyphicon glyphicon-th-list"></span>'
//				,'product' => array('url' => '/products.html', 'name' => 'Products', 'icon' => '')
			)
			,'System' => array(
				'icon' => '<span class="glyphicon glyphicon-cog"></span>'
				,'useraccounts' => array('url' => '/useraccounts.html', 'name' => 'User Accounts', 'icon' => '')
				,'stores' => array('url' => '/stores.html', 'name' => 'Stores', 'icon' => '')
			)
			,'help' => array(
				'name' => 'Help',
				'url' => '/help.html',
				'icon' => '<span class="glyphicon glyphicon-info-sign"></span>'
			)
		);
		$html = "<ul class='nav navbar-nav'>";
			foreach($array as $key => $item)
			{
				$hasNextLevel = !isset($item['name']) && is_array($item) && count($item) > 0;
				$activeClass = ($pageItem === $key || array_key_exists($pageItem, $item) ? 'active' : '');
				$html .= "<li class='" . $activeClass . " visible-xs visible-sm visible-md visible-lg'>";
				$html .= "<a href='" . ($hasNextLevel === true ? '#' : $item['url']) . "' " . ($hasNextLevel === true ? 'class="dropdown-toggle" data-toggle="dropdown"' : '') . ">";
					$html .= (isset($item['icon']) ? $item['icon'] . ' ' : '') . ($hasNextLevel === true ? $key .'<span class="caret"></span>' : $item['name']);
				$html .= "</a>";
					if($hasNextLevel === true)
					{
						$html .= "<ul class='dropdown-menu'>";
						foreach($item as $k => $i)
						{
							if(is_string($i) || !isset($i['url']))
								continue;
							$html .= "<li class='" . ($pageItem === $k ? 'active' : '') . "'><a href='" . $i['url'] . "'>" . (isset($i['icon']) ? $i['icon'] . ' ' : '') .$i['name'] . "</a></li>";
						}
						$html .= "</ul>";
					}
				$html .= "</li>";
			}
		$html .= "</ul>";
		return $html;
	}
}
?>
