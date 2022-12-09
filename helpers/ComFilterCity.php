<?php

/***********************************************************************************************************************
 * ╔═══╗ ╔══╗ ╔═══╗ ╔════╗ ╔═══╗ ╔══╗  ╔╗╔╗╔╗ ╔═══╗ ╔══╗   ╔══╗  ╔═══╗ ╔╗╔╗ ╔═══╗ ╔╗   ╔══╗ ╔═══╗ ╔╗  ╔╗ ╔═══╗ ╔╗ ╔╗ ╔════╗
 * ║╔══╝ ║╔╗║ ║╔═╗║ ╚═╗╔═╝ ║╔══╝ ║╔═╝  ║║║║║║ ║╔══╝ ║╔╗║   ║╔╗╚╗ ║╔══╝ ║║║║ ║╔══╝ ║║   ║╔╗║ ║╔═╗║ ║║  ║║ ║╔══╝ ║╚═╝║ ╚═╗╔═╝
 * ║║╔═╗ ║╚╝║ ║╚═╝║   ║║   ║╚══╗ ║╚═╗  ║║║║║║ ║╚══╗ ║╚╝╚╗  ║║╚╗║ ║╚══╗ ║║║║ ║╚══╗ ║║   ║║║║ ║╚═╝║ ║╚╗╔╝║ ║╚══╗ ║╔╗ ║   ║║
 * ║║╚╗║ ║╔╗║ ║╔╗╔╝   ║║   ║╔══╝ ╚═╗║  ║║║║║║ ║╔══╝ ║╔═╗║  ║║─║║ ║╔══╝ ║╚╝║ ║╔══╝ ║║   ║║║║ ║╔══╝ ║╔╗╔╗║ ║╔══╝ ║║╚╗║   ║║
 * ║╚═╝║ ║║║║ ║║║║    ║║   ║╚══╗ ╔═╝║  ║╚╝╚╝║ ║╚══╗ ║╚═╝║  ║╚═╝║ ║╚══╗ ╚╗╔╝ ║╚══╗ ║╚═╗ ║╚╝║ ║║    ║║╚╝║║ ║╚══╗ ║║ ║║   ║║
 * ╚═══╝ ╚╝╚╝ ╚╝╚╝    ╚╝   ╚═══╝ ╚══╝  ╚═╝╚═╝ ╚═══╝ ╚═══╝  ╚═══╝ ╚═══╝  ╚╝  ╚═══╝ ╚══╝ ╚══╝ ╚╝    ╚╝  ╚╝ ╚═══╝ ╚╝ ╚╝   ╚╝
 *----------------------------------------------------------------------------------------------------------------------
 * @author     Gartes | sad.net79@gmail.com | Telegram : @gartes
 * @date       16.11.22 16:36
 * Created by PhpStorm.
 * @copyright  Copyright (C) 2005 - 2022 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 **********************************************************************************************************************/

namespace OsmapBackgroundHelper;
use CustomfiltersTableSetting_city;
use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;

/**
 * Создание карты сайта для фильтра городов
 * @since 3.9
 */
class ComFilterCity extends BackgroundComponent
{
	protected $component = 'com_filter_city';
	/**
	 * @var int - Itemid пункта меню для option=com_customfilters & view=products
	 * @since 3.9
	 */
	protected  $customfiltersItemId ;
	/**
	 * @var \CustomfiltersModelSetting_city
	 * @since 3.9
	 */
	protected $SettingCityModel ;
	/**
	 * @var array Массив sef ссылок для фильтра
	 * @since 3.9
	 */
	protected $vmSefCategory = [] ;
	/**
	 * @var array Найденные включенные регионы
	 * @since 3.9
	 */
	protected $findRegions = [] ;
	/**
	 * @var int - Id CityFilter
	 * @since 3.9
	 */
	protected $idFilterCity;
	/**
	 * @var string - Системное имя фильтра
	 * @since 3.9
	 */
	protected $slug_filter;
	/**
	 * @var array - Статистика - для разделов фильтра
	 * @since 3.9
	 */
	protected $statistic;

	/**
	 * @throws Exception
	 * @since 3.9 
	 */
	public function __construct()
	{
		\JLoader::register('seoTools_uri' , JPATH_ROOT .'/components/com_customfilters/include/seoTools_uri.php');

		/**
		 * Добавить пуит к моделям компонента
		*/
		\JModelLegacy::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_customfilters/models/setting_city.php' );
		$this->SettingCityModel = \JModelLegacy::getInstance( 'Setting_city', 'CustomfiltersModel' );



	    parent::__construct();
	}

	/**
	 * Удаление файла фильтра и удаление статистики
	 * @throws Exception
	 * @since 3.9
	 */
	public function _removeSiteMapComponent( array &$name , bool $updateMap = false ){

		parent::removeSiteMapComponent($name['fileMapName'] , $updateMap );
		unset( $name['fileMapName'] );
		unset( $name['LastModified'] );
		unset( $name['urlLocCount'] );
  
	}
	/**
	 * Создать файл карты сайта для одного фильтра cityFilter
	 *
	 * @param   int  $idFilterCity  ID FilterCity - для которого создавать карту
	 *
	 * @return array - данные статистики
	 * @throws Exception
	 * @since 3.9
	 */
	public function createMapCityFilter( int $idFilterCity ){
		$this->idFilterCity = $idFilterCity ;
		/**
		 * @var CustomfiltersTableSetting_city
		 */
		$table = $this->SettingCityModel->addToCartFilter($idFilterCity);
		$this->slug_filter = $table->slug_filter ;

		 


		$Registry = new Registry();
		$Registry->loadString( $table->params ) ;
		$paramsArea = $Registry->toArray();
        
		// Создать Sef ссылку для категорий VM выбранных в CityFilter etc/ /filtr/metallocherepitsa/
		foreach ( $table->vm_categories_id as $i => $item)
		{
			$this->vmSefCategory[] = \seoTools_uri::getLinkFilterCategory($item);
		}#END FOREACH

		$this->getOnArea( $paramsArea['use_city_setting'] ) ;

		$arrRegions = $this->getChildren( $this->findRegions );


		$fileMapName = $this->component . '-' . $this->slug_filter .'-id-' . $this->idFilterCity ;

		$this->_removeSiteMapComponent( $table->statistic['FilterArea']  ) ;

		if ( !empty( $arrRegions ))
		{
			$table->statistic['FilterArea'] = $this->addToMapFilterArea( $arrRegions );
			$table->statistic['FilterArea']['urlLocCount'] = $this->urlLocCount ;
			$this->_reset();
		}#END IF

		$this->_removeSiteMapComponent( $table->statistic['FilterCustoms']  ) ;


		// Дополнительные параметры CityFilter
		$params_customs = json_decode( $table->params_customs );
		if ( !empty($params_customs) )
		{
			$table->statistic['FilterCustoms'] = $this->addToMapFilterAreaCustoms( $params_customs );
			$table->statistic['FilterCustoms']['urlLocCount'] = $this->urlLocCount ;
			$this->_reset();
		}#END IF
		$statistic = $table->statistic;
		$table->statistic = json_encode( $table->statistic ) ;
		$table->store();

		return $statistic ;
	}

	/**
	 * Создать карту сайта для выбранного CityFilter параметры Custom - и пересоздать sitemap-root.xml
	 * @throws Exception
	 * @since 3.9
	 */
	protected function addToMapFilterAreaCustoms( $params_customs ):array
	{
		// Сбрасываем количество ссылок <url><loc>....<loc><url>
		$this->urlLocCount = 0 ;
		// меняем имя фильтра для custom параметров
		$this->slug_filter .= '-custom' ;

		// Крутим SEF ссылки на категории VM
		foreach (  $this->vmSefCategory as $vmSefCategoryItem )
		{
			// Крутим параметры Customs из CityFilter
			foreach (  $params_customs as $item  )
			{
				$this->addLinkCollect( $vmSefCategoryItem . $item->sef_alias ) ;
			}
		}
		return $this->writeFileXmlComponent();
	}
	/**
	 * Добавить в карту сайта регионы(города) для фильтра
	 *
	 * @param $arrArea
	 *
	 * @return array
	 * @throws Exception
	 * @since version
	 */
	protected function addToMapFilterArea($arrArea):array
	{
		// Крутим SEF ссылки на категории VM
		foreach (  $this->vmSefCategory as $vmSefCategoryItem )
		{
			// Крутим включенные города в фильтре CityFilter
			foreach (  $arrArea as $itemArea )
			{
				$this->addLinkCollect( $vmSefCategoryItem . $itemArea->chc_alias ) ;
			}#END FOREACH
		}#END FOREACH
		return $this->writeFileXmlComponent();
	}

	/**
	 * Создать файл для компонента /sitemap-com_* и пересоздать общий файл sitemap-root.xml
	 * @return  array
	 * @throws Exception
	 * @since 3.9
	 */
	protected function writeFileXmlComponent():array
	{
		$returnData = [
			'fileMapName' => false ,
			'LastModified' => false ,
			'urlLocCount' => 0 ,
		];
		if ( !$this->urlLocCount ) return $returnData ; #END IF

		$fileMapName = $this->writeFileMap( $this->slug_filter .'-id' , $this->idFilterCity  );
		$resultData = $this->createFileAllMapXml();

		$returnData['fileMapName'] = $fileMapName ;
		$returnData['LastModified'] = $resultData['LastModified'][$fileMapName] ;
		$returnData['urlLocCount'] = $resultData['urlLocCount'] ;
		return $returnData ;
	}

	/**
	 * Добавляем в коллекцию ссылок <url><loc>....<loc><url>
	 * @param $localUrl
	 *
	 * @return void
	 * @since 3.9
	 */
	protected function addLinkCollect( $localUrl ){
		$uri = new \Joomla\Uri\Uri( $localUrl );
		$jRoot = preg_replace( '/\/$/' , '' , \JUri::root() );
		$uri->setHost( $jRoot );
		$url = $uri->toString() ;
		$this->addUrlLocTag($url , false );
	}

	/**
	 * Найти дочерние регионы
	 *
	 * @param $aliasArr
	 *
	 * @return bool|array - массив с городами
	 * @since 3.9
	 */
	protected function getChildren($aliasArr){

		if ( empty( $aliasArr ) ) return false ;#END IF

		$Query = $this->db->getQuery( true);
		$select = [
			'pc.id AS pc_id',
			'pc.name AS pc_name',
			'pc.alias AS pc_alias',
			'pc.parent_id AS pc_parent_id',
			'chc.id AS chc_id',
			'chc.name AS chc_name',
			'chc.alias AS chc_alias',
			'chc.parent_id AS chc_parent_id',
		];
		$Query->select($select )
			->from($this->db->quoteName('#__cf_customfields_city' , 'pc'));
		$Query->leftJoin(
			$this->db->quoteName('#__cf_customfields_city' , 'chc' ) . 'ON'
			. $this->db->quoteName('chc.parent_id') .'='. $this->db->quoteName('pc.id')
		);

		$_aliasArr = array_map([$this->db, 'quote'], $aliasArr );
		$where = [
			sprintf('pc.alias IN (%s)', join(',', $_aliasArr)) ,
			$this->db->quoteName('chc.id') .'>'.$this->db->quote(0  )
		];
		$Query->where( $where);
//		echo '<br>------------<br>Query Dump :'.__FILE__ .' '.__LINE__ .$Query->dump().'------------<br>';
		$this->db->setQuery($Query);

		$res = $this->db->loadObjectList();

		// убрать из включенных регионов те которые являются областями
		foreach ( $res as $resItem)
		{
			if ( in_array( $resItem->pc_alias , $aliasArr ))
			{
				$key = array_search($resItem->pc_alias , $aliasArr );
				unset( $aliasArr[$key]);
			}#END IF
		}#END FOREACH

		// находим регионы у которых нет дочерних etc/ Киев
		$Query = $this->db->getQuery( true);
		$select = [
			'chc.id AS chc_id',
			'chc.name AS chc_name',
			'chc.alias AS chc_alias',
			'chc.parent_id AS chc_parent_id',
		];
		$Query->select($select )
			->from($this->db->quoteName('#__cf_customfields_city' , 'chc'));
		$_aliasArr = array_map([$this->db, 'quote'], $aliasArr );
		$where = [
			sprintf('chc.alias IN (%s)', join(',', $_aliasArr)) ,
			$this->db->quoteName('chc.id') .'>'.$this->db->quote(0  )
		];
		$Query->where( $where);
		$this->db->setQuery($Query);
		$resAreaNoChild = $this->db->loadObjectList();
		// Объединить города
		return array_merge( $res, $resAreaNoChild);
	}



	/**
	 * Найти города во включенных регионах
	 * @param array $paramsArea Массив с настройками городов
	 * @param bool $_use -- в замыкании - передаем использование
	 *
	 * @return void
	 * @since 3.9
	 */
	protected function getOnArea($paramsArea , $_use = false ){


		foreach ( $paramsArea as $keyArea => $item)
		{
			// Если у региона только use == 0
			if ( key_exists('use' , $item ) && count( $item ) == 1 && $item['use'] == 0 ) continue ;  #END IF

			if ( key_exists('use' , $item ) && count( $item ) == 1 && $item['use'] == 1 )
			{
				$this->findRegions[] = $keyArea ;
				continue ;
			}
			else if ( key_exists('use' , $item ) && count( $item ) > 1 && $item['use'] == 1 ){
				foreach ( $item as $childKeyArea => $childArr)
				{
					if ( $childKeyArea == 'use') continue ; #END IF
					$this->findRegions[] = $childKeyArea ;
				}#END FOREACH
			}#END IF

			if ( !key_exists('use' , $item ) ) $item['use'] = 0 ;  #END IF

			if (is_array($item))
			{
				$this->getOnArea($item);
			}#END IF



		}#END FOREACH

//		echo'<pre>';print_r( $paramsArea );echo'</pre>'.__FILE__.' '.__LINE__;
//		die(__FILE__ .' '. __LINE__ );

	}

}