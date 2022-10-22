<?php

use OsmapBackgroundHelper\BackgroundComponent;

JLoader::registerNamespace( 'OsmapBackgroundHelper' , JPATH_ADMINISTRATOR . '/modules/mod_osmap_background_toolbar/helpers' , $reset = false , $prepend = false , $type = 'psr4' );

$config = \Joomla\CMS\Factory::getConfig();
$config->set('debug' , 1 );
$config->set('error_reporting' , 'development' );

class ModOsmapBackgroundToolbarHelper extends BackgroundComponent
{
	/**
	 * Прокси создание All Map Xml
	 *
	 * @since version
	 */
    public static function createFileAllMapXmlAjax(){
        $helperBackgroundComponent = new \OsmapBackgroundHelper\BackgroundComponent();
	    $returnData = $helperBackgroundComponent->createFileAllMapXml();

	    echo new JResponseJson( $returnData , 'Основной файл карты сайта создан' , false );
	    die();
    }

    /**
     * Создать карту для Seo Фильтра
     * @return void
     * @throws Exception
     * @since 3.9
     */
    public static function createComFilterMapAjax(){

        $helperComFilter = new  \OsmapBackgroundHelper\ComFilter();
        $returnData =   $helperComFilter->createFilterMap() ;
        echo new JResponseJson( $returnData , 'Ссылки фильтра добавлены в карту сайта' , false );
        die();
    }

    /**
     * Создание карты для товаров
     * @return void
     * @throws Exception
     * @since 3.9
     */
    public static function onCreateMapXmlProductsAjax(){
        $helperVirtuemart = new  \OsmapBackgroundHelper\Virtuemart();
        $returnData = [
            'DataProducts' => $helperVirtuemart->onCreateMapXmlProducts() ,
            'pluginVirtuemartSetting' => self::getPluginSetting() ,
        ];
        echo new JResponseJson( $returnData , 'Товары добавлены в карту сайта' , false );
        die();
    }
    /**
     * Создать файл sitemap-com_virtuemart-category-{1}.xml
     * @return void
     * @since 3.9
     */
    public static function onCreateMapXmlCategoryAjax(){
        $helperVirtuemart = new  \OsmapBackgroundHelper\Virtuemart();
        $returnData = [
            'categories' => $helperVirtuemart->onCreateMapXmlCategory() ,
//            'categories' => $helperVirtuemart->getCategories() ,
            'pluginVirtuemartSetting' => self::getPluginSetting(),
        ];
        echo new JResponseJson( $returnData , 'Категории добавлены в карту сайта' , false );
        die();
    }

    /**
     * Получить список всех категорий
     * @return void
     * @since 3.9
     */
    public static function getCategoryIdListAjax(){
        $helperVirtuemart = new  \OsmapBackgroundHelper\Virtuemart();
        $returnData = [
            'categories' => $helperVirtuemart->getListCategory() ,
//            'categories' => $helperVirtuemart->getCategories() ,
            'pluginVirtuemartSetting' => self::getPluginSetting(),
        ];
        echo new JResponseJson( $returnData , 'Найденные категории' , false );
        die();

    }

	/**
	 * Создать файл для всех продуктов VM  (Получить список всех продуктов)
	 * @return void
	 * @throws Exception
	 * @since 3.9
	 */
    public static function getProductsLinkListAjax(){
        $helperVirtuemart = new  \OsmapBackgroundHelper\Virtuemart();
        $listProduct = $helperVirtuemart->getLisProducts() ;
        $returnData = [
            'LisProducts'=> $listProduct ,
            'countProducts'=> count( $listProduct ) ,
        ];
        echo new JResponseJson( $returnData , 'Найденные товары' , false );
        die();
    }


}