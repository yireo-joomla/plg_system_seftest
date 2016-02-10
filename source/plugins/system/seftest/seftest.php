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
	 * Event method onAfterInitialise
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function onAfterInitialise()
	{
		$application = JFactory::getApplication();
		$router = $application->getRouter();
		$sef = $application->getUserStateFromRequest('plugin.seftest', 'sef', null);
		$config = JFactory::getConfig();

		if ($application->getName() != 'site')
		{
			return true;
		}

		if (!isset($sef) || $sef == $config->get('sef'))
		{
			return true;
		}

		if ($sef == 1)
		{
			$router->setMode(JROUTER_MODE_SEF);
		}
		elseif ($sef == 0)
		{
			$router->setMode(JROUTER_MODE_RAW);
		}

		return true;
	}

	/**
	 * Event method onAfterRender
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function onAfterRender()
	{
		$application = JFactory::getApplication();

		if ($application->getName() != 'site')
		{
			return true;
		}

		$box_show = $this->params->get('show', '1');
		$box_position = $this->params->get('position', 'top:0;left:0');
		$box_foreground = $this->params->get('foreground', '#000000');
		$box_background = $this->params->get('background', '#ffffff');

		if ($box_show != 1)
		{
			return true;
		}

		$sef = $application->getUserStateFromRequest('plugin.seftest', 'sef', null);
		$selected = ($sef == 1) ? ' selected="selected"' : '';

		$styles = array(
			'border:1px solid ' . $box_foreground,
			'color:' . $box_foreground,
			'background-color:' . $box_foreground,
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

		$buffer = JResponse::getBody();
		$buffer = str_replace('</body>', $box . '</body>', $buffer);
		JResponse::setBody($buffer);

		return true;
	}
}
