<?php
/**
 * @package   OSMap
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2007-2014 XMap - Joomla! Vargas - Guillermo Vargas. All rights reserved.
 * @copyright 2016-2021 Joomlashack.com. All rights reserved.
 * @license   https://www.gnu.org/licenses/gpl.html GNU/GPL
 *
 * This file is part of OSMap.
 *
 * OSMap is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * OSMap is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OSMap.  If not, see <https://www.gnu.org/licenses/>.
 */




defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Joomla\CMS\Installer\Adapter\ModuleAdapter;


class mod_osmap_background_toolbarInstallerScript
{
	/**
	 * Отметить для отмены установки
	 * ---
	 * Flag to cancel the installation
	 * ---
	 *
	 * @var bool
	 * @since 3.9
	 */
	protected $cancelInstallation = false;

	/**
	 * Этот метод вызывается после установки компонента.
	 * ---
	 * This method is called after a component is installed.
	 * ---
	 *
	 * @param   stdClass  $parent  - Parent object calling this method.
	 *
	 * @return void
	 * @since 3.9
	 */
	public function install(stdClass $parent)
	{

//		$parent->getParent()->setRedirectURL('index.php?option=com_helloworld');
//		die(__FILE__ .' '. __LINE__ );

	}

	/**
	 * Этот метод вызывается после удаления компонента.
	 * ---
	 * This method is called after a component is uninstalled.
	 * ---
	 *
	 * @param   stdClass  $parent  - Parent object calling this method.
	 *
	 * @return void
	 * @since 3.9
	 */
	public function uninstall(stdClass $parent)
	{
		echo '<p>' . Text::_('COM_HELLOWORLD_UNINSTALL_TEXT') . '</p>';
	}

	/**
	 * Этот метод вызывается после обновления компонента.
	 * ---
	 * This method is called after a component is updated.
	 * ---
	 *
	 * @param   Joomla\CMS\Installer\Adapter\ModuleAdapter  $parent  - Parent object calling object.
	 *
	 * @return void
	 * @since 3.9
	 */
	public function update( Joomla\CMS\Installer\Adapter\ModuleAdapter $parent)
	{
		// Обновление до версии - ( %s )
		echo '<p>' . Text::sprintf('MOD_OSMAP_BACKGROUND_TOOLBAR_UPDATE', (string)$parent->get('manifest')->version ) . '</p>';
	}

	/**
	 * Запускается непосредственно перед выполнением каких-либо действий по установке компонента.
	 * ---
	 * В этой функции должны выполняться проверки и предварительные условия.
	 * ---
	 * Runs just before any installation action is performed on the component.
	 * Verifications and pre-requisites should run in this function.
	 *
	 *
	 * @param   string                                      $type    - Type of PreFlight action. Possible values are:
	 *                           - * install
	 *                           - * update
	 *                           - * discover_install
	 * @param   Joomla\CMS\Installer\Adapter\ModuleAdapter  $parent  - Parent object calling object.
	 *
	 * @return void
	 * @since 3.9
	 */
	public function preflight(string $type,  $parent)
	{

//		$language = JFactory::getLanguage();
//		echo'<pre>';print_r( $language );echo'</pre>'.__FILE__.' '.__LINE__;

		// Старт обновления
		echo '<p>' . JText::_('MOD_OSMAP_BACKGROUND_TOOLBAR_PREFLIGHT_' . mb_strtoupper($type) ) . '</p>';
//		die(__FILE__ .' '. __LINE__ );

//		echo'<pre>';print_r( $parent );echo'</pre>'.__FILE__.' '.__LINE__;
		
//		die(__FILE__ .' '. __LINE__ );


	}

	/**
	 * Запускается сразу после выполнения любого действия по установке компонента.
	 * ---
	 * Runs right after any installation action is performed on the component.
	 * ---
	 *
	 * @param   string    $type    - Type of PostFlight action. Possible values are:
	 *                           - * install
	 *                           - * update
	 *                           - * discover_install
	 * @param   Joomla\CMS\Installer\Adapter\ModuleAdapter  $parent  - Parent object calling object.
	 *
	 * @return void
	 * @since 3.9
	 */
	public function postflight(string $type, ModuleAdapter  $parent)
	{

		$this->createModule( $parent );
		// Обновление успешно завершено
		echo '<p>' . JText::_('MOD_OSMAP_BACKGROUND_TOOLBAR_POSTFLIGHT_' . mb_strtoupper($type) ) . '</p>';
	}

	/**
	 * @param $parent
	 *
	 * @return void
	 * @since 3.9
	 */
	protected function createModule( $parent ){
		$db = JFactory::getDBO();
		$Query = $db->getQuery(true);

		$table = $db->quoteName('#__modules') ;
		$columns = [
			'position',
			'published',
			'access','ziffilter_value_fild'];


		// mypanel
		$db->setQuery("UPDATE `#__modules`".
			" SET 
			`position` = 'panel', 
			`published` = '1', 
			`access` = '3'".
			" WHERE `#__modules`.`module` = 'mod_mypanel'; 
			");

		if (!$db->query() && ($db->getErrorNum() != 1060)) {
			echo $db->getErrorMsg(true);
		}
//		die(__FILE__ .' '. __LINE__ );

	}

}
