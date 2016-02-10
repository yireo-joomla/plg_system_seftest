<?php
/**
 * Joomla! sef-test plugin
 *
 * @author    Yireo (info@yireo.com)
 * @package   SEFTest
 * @copyright Copyright 2015
 * @license   GNU Public License
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

// @deprecated: Import the parent library
jimport('joomla.plugin.plugin');

/**
 * Class PlgSystemSefTest
 */
class PlgSystemSefTest extends JPlugin
{
	/**
	 * @var JApplicationSite
	 */
	protected $app;

	/**
	 * Event method onAfterInitialise
	 *
	 * @return null
	 * @throws Exception
	 */
	public function onAfterInitialise()
	{
		$router = $this->app->getRouter();
		$sef = $this->app->getUserStateFromRequest('plugin.seftest', 'sef', null);
		$config = JFactory::getConfig();

		if ($this->allowPlugin() == false)
		{
			return;
		}

		if ($sef !== null || $sef == $config->get('sef'))
		{
			return;
		}

		if ($sef == 1)
		{
			$router->setMode(JROUTER_MODE_SEF);
		}
		elseif ($sef == 0)
		{
			$router->setMode(JROUTER_MODE_RAW);
		}

		return;
	}

	/**
	 * Allow the usage of this plugin
	 *
	 * @return bool
	 */
	protected function allowPlugin()
	{
		if ($this->app->getName() != 'site')
		{
			return false;
		}

		return true;
	}

	/**
	 * Event method onAfterRender
	 *
	 * @return null
	 * @throws Exception
	 */
	public function onAfterRender()
	{
		if ($this->allowPlugin() == false)
		{
			return;
		}

		if ((int) $this->params->get('show', 1) !== 1)
		{
			return;
		}

		$box = $this->getSelectBoxHtml();
		$buffer = JResponse::getBody();
		$buffer = str_replace('</body>', $box . '</body>', $buffer);
		JResponse::setBody($buffer);

		return;
	}

	/**
	 * Return the HTML of the box
	 */
	protected function getSelectBoxHtml()
	{
		$box_position = $this->params->get('position', 'top:0;left:0');
		$box_foreground = $this->params->get('foreground', '#000');
		$box_background = $this->params->get('background', '#fff');

		$sef = $this->app->getUserStateFromRequest('plugin.seftest', 'sef', null);
		$selected = ($sef == 1) ? ' selected="selected"' : '';

		$styles = array(
			'border:1px solid ' . $box_foreground,
			'color:' . $box_foreground,
			'background-color:' . $box_background,
			'position:absolute',
			$box_position,
			'padding:10px',
			'margin:10px');

		$style = implode(';', $styles);

		$box = '<div style="' . $style . '">';
		$box .= '<form method="post" id="seftest" name="seftest" action="' . JURI::current() . '">';
		$box .= '<select name="sef" onchange="document.seftest.submit();">';
		$box .= '<option value="0">' . JText::_('SEF Disabled') . '</option>';
		$box .= '<option value="1"' . $selected . '>' . JText::_('SEF Enabled') . '</option>';
		$box .= '</select>';
		$box .= '</form>';
		$box .= '</div>';

		return $box;
	}
}
