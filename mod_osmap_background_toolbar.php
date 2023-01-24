<?php
/**
 * @package    mod_osmap_background_toolbar
 *
 * @author     Gartes <your@email.com>
 * @copyright  A copyright
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       http://your.url.com
 */

use GNZ11\Core\Js;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Helper\ModuleHelper;



defined('_JEXEC') or die;
/** @var stdClass $params*/
// The below line is no longer used in Joomla 4
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

/**
 * Получить версию модуля
 */
$xml_file = JPATH_ROOT .  '/administrator/modules/mod_osmap_background_toolbar/mod_osmap_background_toolbar.xml' ;
$dom = new DOMDocument("1.0", "utf-8");
$dom->load($xml_file);
$__v =   $dom->getElementsByTagName('version')->item(0)->textContent;



// Get the active languages for multi-language sites
$languages = null;
$languagesSef = [];
if (Joomla\CMS\Language\Multilanguage::isEnabled()) {
	$languages = \Joomla\CMS\Language\LanguageHelper::getLanguages();

	foreach ( $languages as $language)
	{
		$languagesSef[] = $language->sef ;
	}#END FOREACH
}










try
{
	$patchGnz11 = JPATH_LIBRARIES . '/GNZ11' ;
    JLoader::registerNamespace('GNZ11', $patchGnz11 , $reset = false, $prepend = false, $type = 'psr4');
    $GNZ11_js = Js::instance();
} catch (Exception $e)
{


	$app = \Joomla\CMS\Factory::getApplication();


    if (!\Joomla\CMS\Filesystem\Folder::exists( $patchGnz11 ) && $app->isClient('administrator'))
    {
        $app->enqueueMessage('The GNZ11 library must be installed', 'error');
		return ;
    }#END IF
}
require_once JPATH_ADMINISTRATOR . '/components/com_osmap/include.php';

$plugins = \Alledia\OSMap\Helper\General::getPluginsFromDatabase();




// Добавить псевдоплагин для создания ссылок пунктов меню
$com_menu = new stdClass();
$com_menu->element = 'com_menu' ;
array_unshift($plugins , $com_menu );


// Добавляем псевдо-плагин для фильтра
$com_filter = new stdClass();
$com_filter->element = 'com_filter' ;
$plugins[] = $com_filter ;





$osmapParams = ComponentHelper::getParams('com_osmap');


$doc = \Joomla\CMS\Factory::getDocument();
$opt = [
    // Медиа версия
    '__v' => $__v ,
    // Режим разработки
    'development_on' => false,
    // URL - Сайта
    'URL' => JURI::root(),
//    'dataType' => 'html' ,
    'plugins' => $plugins,
    'languagesSef' => $languagesSef,
    // Максимальное количество ошибок для Ajax запроса
    'maxAjaxErr' => $osmapParams->get('max_ajax_err' , 5 ),
	'modOsmapBackgroundToolbar_params' => $params->toArray() ,

];
$doc->addScriptOptions('mod_osmap_background_tool', $opt);
Js::addJproLoad(
	\Joomla\CMS\Uri\Uri::root()
	. '/administrator/modules/mod_osmap_background_toolbar/assets/js/mod_osmap_background_toolbar.js?'.$__v, false, false);


require ModuleHelper::getLayoutPath('mod_osmap_background_toolbar', $params->get('layout', 'default'));
