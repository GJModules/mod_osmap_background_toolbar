<?php

JLoader::registerNamespace( 'OsmapBackgroundHelper' , JPATH_ADMINISTRATOR . '/modules/mod_osmap_background_toolbar/helpers' , $reset = false , $prepend = false , $type = 'psr4' );

class ModOsmapBackgroundToolbarHelper
{

    public static function createFileAllMapXmlAjax(){
        $helper = new \OsmapBackgroundHelper\BackgroundComponent();
        $helper->createFileAllMapXml();
    }

    /**
     * Создать карту для Seo Фильтра
     * @return void
     * @throws Exception
     * @since 3.9
     */
    public static function createComFilterMapAjax(){


        $helperComFilter = new  \OsmapBackgroundHelper\ComFilter();
        $returnData = [
            'ComFilterData' => $helperComFilter->createFilterMap(),
        ];
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
     * Получить список вссех продуктов
     * @return void
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

    protected static function getPluginSetting(){
        $plugin = \Joomla\CMS\Plugin\PluginHelper::getPlugin('osmap', 'com_virtuemart');
        $Registry = new \Joomla\Registry\Registry();
        return $Registry->loadObject( json_decode( $plugin->params ))->toArray();
    }
}