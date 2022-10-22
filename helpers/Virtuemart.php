<?php

namespace OsmapBackgroundHelper;

use Exception;
use Joomla\CMS\Filesystem\File as JFile;
use JRoute;
use Joomla\CMS\Language\LanguageHelper;
use Joomla\CMS\Language\Multilanguage;

class Virtuemart extends BackgroundComponent
{

    protected $component = 'com_virtuemart';

	/**
	 * @var array save category data
	 * @since version
	 */
	protected $categoryResult = [];

	protected $categoryResultMultilanguage  = [] ;
	/**
	 * @var array - хранение найденных товаров
	 * @since version
	 */
	protected $productsResult = [] ;

	public function __construct()
    {
        if (!class_exists('VmConfig')) require_once JPATH_ADMINISTRATOR . '/components/com_virtuemart/helpers/config.php';
        \VmConfig::loadConfig();

		parent::__construct();


    }

    public $childCategoriesId = array();
    /**
     * Способ получить все доступные категории
     *
     * Method to get all available categories
     *
     * @category Webkul
     * @author Webkul
     * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
     * @license https://store.webkul.com/license.html
     *
     * return object
     * @since 3.9
     */
    public function getCategories()
    {
		die(__FILE__ .' '. __LINE__ );

        $categoryData = array();
        $categoryModel = \VmModel::getModel('category');
        $categories = $categoryModel->getCategoryTree(0, 0, false);
        foreach ($categories as $category) {
            $categoryData[] = $this->getCategoryData($category->virtuemart_category_id);
        }
        $this->childCategoriesId = array_unique($this->childCategoriesId);
        foreach ($this->childCategoriesId as $childCategoryId) {
            foreach ($categoryData as $catKey => $catValue) {
                if ($catValue['virtuemartCategoryId'] == $childCategoryId) {
                    unset($categoryData[$catKey]);
                }
            }
        }
        $categoryData = array_values($categoryData);
        return $categoryData;
    }
    /**
     * Получить данные о категории
     * Эта функция рекурсивно вызывает себя, чтобы получить все связанные данные дочерней категории
     *
     * Get category data
     * This function calls itself recursively to get all related child category data
     *
     * @category Webkul
     * @author Webkul
     * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
     * @license https://store.webkul.com/license.html
     *
     * @param int $categoryId category id
     *
     * @return array
     * @since 3.9
     */
    public function getCategoryData($categoryId)
    {
        $categoryModel = \VmModel::getModel('category');
        $categoryData = array();
        $tempCategory = $categoryModel->getCategory($categoryId);


        
        
        //fetch all relevant data required
        $categoryData['virtuemartCategoryId'] = $categoryId;
        // Создать SEF ссылку на категорию
        $url = 'index.php?option=com_virtuemart&view=category&virtuemart_category_id='.$categoryId.'&virtuemart_manufacturer_id=0&lang=de';
        $categoryData['sef'] = JRoute::link('site', $url )  ;
        $categoryData['ItemId'] = $this->getItemId( $categoryData['sef'] , $categoryId ) ;

		echo'<pre>';print_r( $categoryData );echo'</pre>'.__FILE__.' '.__LINE__;
		die(__FILE__ .' '. __LINE__ );


        $categoryData['categoryName'] = $tempCategory->category_name;
        $categoryData['children'] = array();
        if ($tempCategory->children != null)
        {
            foreach ($tempCategory->children as $childCatkey => $childCatValue)
            {
                $this->childCategoriesId[] = $childCatValue->virtuemart_category_id;
                $categoryData['children'][] = $this->getCategoryData($childCatValue->virtuemart_category_id);
            }
        }
        $tempCategory = null;
        return $categoryData;
    }
    /**
     * Получить ItemId - меню для категории товаров по SEF ссылке
     * @param string $categorySefLink - SEF на категорию
     * @return int|null - ItemId
     * @since 3.9
     */
    protected function getItemId(string $categorySefLink , $categoryId )
    {
        $Query = $this->db->getQuery(true);
        $selectArr = [
            $this->db->quoteName('id'),
        ];
        $Query->select($selectArr);
        $Query->from( $this->db->quoteName('#__menu'));
//        $Query->where($this->db->quoteName('link' ) . '=' . $this->db->quote( $categorySefLink ) );
        $url  = 'index.php?option=com_virtuemart' ;
        $url .= '&view=category';
        $url .= '&virtuemart_category_id='.$categoryId ;
        $url .= '&virtuemart_manufacturer_id=0';
        $Query->where($this->db->quoteName('link' ) . '=' . $this->db->quote( $categorySefLink ) );

        $this->db->setQuery($Query);
        /*if ( $categorySefLink == '/catalog/metalocherepitsa/')
        {
            echo $Query ;
            die(__FILE__ .' '. __LINE__ );
        }#END IF*/
        return $this->db->loadResult();
    }


	/**
	 * Получить все категории одним списком
	 * @return array|mixed
	 * @throws Exception
	 * @since 3.9
	 *        TODO - Не используется
	 */
    public function getListCategory(){
	    $app = \Joomla\CMS\Factory::getApplication();

	    $Query = $this->db->getQuery(true);
        $selectArr = [
            $this->db->quoteName('vmCat.virtuemart_category_id'),
            $this->db->quoteName('vmCatLang.category_name'),
        ];
        $Query->select($selectArr);
        $Query->from( $this->db->quoteName( '#__virtuemart_categories' , 'vmCat' ));
        $Query->leftJoin(
            $this->db->quoteName( '#__virtuemart_categories_' . $this->language  , 'vmCatLang' )
            .'ON vmCat.virtuemart_category_id = vmCatLang.virtuemart_category_id'
        );

        $whereArr = [ 'vmCat.published = 1' , ];
        $Query->where( $whereArr ) ;

        $this->db->setQuery( $Query );
        $categoryResult = $this->db->loadObjectList() ;


		// Перебираем категории создаем SEF ссылку
        foreach ( $categoryResult as &$item)
        {
            $url = 'index.php?option=com_virtuemart&view=category'
	            .'&virtuemart_category_id='.$item->virtuemart_category_id
	            .'&virtuemart_manufacturer_id=0'
                // Добавить sef - lang - если Multilanguage ON
	            . ($this->languages ? '&lang='.$this->languages[ $this->countLanguages ]->sef : '')
            ;

	        $sefUrl = JRoute::link('site', $url );
			// Добавить ссылку в коллекцию <url><loc>
	        $this->addUrlLocTag( $sefUrl );

	        $sefUrl = preg_replace('#^\/#' , '' , $sefUrl );
	        $item->sef = \JUri::root() . $sefUrl   ;
			$this->categoryResult[] = $item;

        }#END FOREACH

		// создать карту для категорий
	    $this->writeFileMap(  'category'.( $this->languages ?'-'.$this->languages[ $this->countLanguages ]->sef:'') );

	    // Если Multilanguage ON - Устанавливаем замыкание метода для следующего языка
	    if ( $this->languages && $this->countLanguages != count( $this->languages ) - 1 ){
		    $this->changeLang();

			$this->getListCategory();
 	        return $this->categoryResult  ;
	    }

        return $this->categoryResult  ;
    }

    /**
     * Создать файл/файлы sitemap-com_virtuemart-category-{№}.xml для категорий
     * @return array
     * @throws Exception
     * @since 3.9
     */
    public function onCreateMapXmlCategory(){

		// TODO - Больше не используется
		die(__FILE__ .' '. __LINE__ );

        $app = \Joomla\CMS\Factory::getApplication();
        // Array links category
        $categoryListSlug = $app->input->get('categories' , [], 'ARRAY');
        // Index - номер элемента с которого начинать запись
        $indexCategory = $app->input->get('indexCategory' , 0, 'INT');
        // Номер файла
        $indexFile = $app->input->get('indexFile' , 0, 'INT');

        $iCat = 0 ; 
        foreach (  $categoryListSlug  as $item)
        {
            if ( $indexCategory >  $iCat ) continue ;#END IF
            $indexCategory ++ ;
            $iCat ++ ;
            $this->addUrlLocTag( $item );

        }#END FOREACH

//	    $this->writeFileMap( $indexFile ,'category' );

        $dataReturn = [
            'indexCategory'=> $indexCategory ,
            'indexFile'=> $indexFile ,
            'categoryListSlug'=> $categoryListSlug ,
        ];
        return $dataReturn ;

    }

    /**
     * Создать файл xml - со ссылками на товар
     * @return array
     * @throws Exception
     * @since 3.9
     */
    public function onCreateMapXmlProducts(){

        $app = \Joomla\CMS\Factory::getApplication();

        $limitLinksFile = $app->input->get('limitLinksFile' , 1000, 'INT');
        $offset = $app->input->get('offset' , 0, 'INT');
        // Номер файла
        $indexFile = $app->input->get('indexFile' , 0, 'INT');

        // Получить список товаров
        $ListProducts = $this->getLisProducts( $offset , $limitLinksFile );

        foreach (  $ListProducts  as $item)
        {
            $offset ++ ;
            $this->addUrlLocTag( $item->slugSefUrl );

        }#END FOREACH
        $this->writeFileMap(  'products' );

        $indexFile ++ ;

        $returnData = [
            'offset'=> $offset ,
            'indexFile'=> $indexFile ,
        ];
        return $returnData ;

        
    }

    /**
     * Получить список всех товаров
     * @return array|mixed
     * @throws Exception
     * @since 3.9
     */
    public function getLisProducts( $offset = 0, $limit = 0 ){

        $app = \Joomla\CMS\Factory::getApplication();
        $categoryListSlug = $app->input->get('categoryListSlug' , [], 'ARRAY');


        $Query = $this->db->getQuery(true);
        $selectArr = [
            $this->db->quoteName('p.modified_on'),
            $this->db->quoteName('pc.virtuemart_category_id'),
            $this->db->quoteName('p.virtuemart_product_id'),
//            $this->db->quoteName('pl.product_name'),
            $this->db->quoteName('pl.slug'),

        ];
        $Query->select($selectArr);
        $Query->from( $this->db->quoteName( '#__virtuemart_categories' , 'c' ) );
        $Query->leftJoin(
			$this->db->quoteName( '#__virtuemart_product_categories' , 'pc' )
            . ' ON pc.virtuemart_category_id = c.virtuemart_category_id ');
        $Query->leftJoin(
			'`#__virtuemart_products_' . VMLANG . '` as pl on pl.virtuemart_product_id = pc.virtuemart_product_id');
        $Query->leftJoin( '`#__virtuemart_products` as p on p.virtuemart_product_id = pc.virtuemart_product_id');
        $whereArr = [
            'p.published = 1',
            'c.published = 1 ',
//            'vmProdCat.virtuemart_category_id = '.$dataCategory['virtuemartCategoryId'],
        ];
        $Query->where( $whereArr ) ;
        $Query->group( 'p.virtuemart_product_id' ) ;

        $this->db->setQuery( $Query , $offset  , $limit );
        $productsResult = $this->db->loadObjectList() ;

	    foreach ($productsResult as $item)
	    {
		    $url  = 'index.php?option=com_virtuemart&view=productdetails' ;
		    $url .= '&virtuemart_product_id=' . $item->virtuemart_product_id ;
		    $url .= '&virtuemart_category_id=' . $item->virtuemart_category_id ;
		    // Добавить sef - lang - если Multilanguage ON
		    $url .= ($this->languages ? '&lang='.$this->languages[ $this->countLanguages ]->sef: '') ;

		    $item->sefUrl = JRoute::link('site', $url );

			// добавить товар в коллекцию
			$this->productsResult[] =  $item ;
			// Добавить ссылку в коллекцию <url><loc>
		    $this->addUrlLocTag( $item->sefUrl );

		}#END FOREACH

		// Создать карту для товаров
	    $this->writeFileMap(  'products'.( $this->languages ?'-'.$this->languages[ $this->countLanguages ]->sef:'') );

	    // Если Multilanguage ON - Устанавливаем замыкание метода для следующего языка
	    if ( $this->languages && $this->countLanguages != count( $this->languages ) - 1 ){
		    $this->changeLang();
		    $this->getLisProducts();
	    }
	    return $this->productsResult ;


		// TODO - далее не используем  - нужен тест на МАРКЕТ ПРОФИЛЬ

        foreach ( $productsResult as &$item)
        {

            $SefUrl = $categoryListSlug[$item->virtuemart_category_id] . '/' .$item->slug ;
            $SefUrl = str_replace('//' , '/' , $SefUrl );
            $item->slugSefUrl = $SefUrl;
        }#END FOREACH

        return $productsResult ;




     



        $returnArr = [] ;

        if ( !empty( $productsResult ) )
        {
            echo'<pre>';print_r( $productsResult );echo'</pre>'.__FILE__.' '.__LINE__;
            die(__FILE__ .' '. __LINE__ );
        }#END IF



        foreach ( $productsResult as &$item)
        {
            $url  = 'index.php?option=com_virtuemart&view=productdetails' ;
            $url .= '&virtuemart_product_id=' . $item ;
//            $url .= '&virtuemart_category_id=' . $dataCategory['virtuemartCategoryId'] ;
            if ( !empty( $dataCategory['ItemId'] ) )
            {
//                $url .= '&Itemid='. $dataCategory['ItemId'] ;
            }#END IF

            $live_site = substr(\JURI::root(), 0, -1);
            $app    = \JApplication::getInstance('site');
            $router = $app->getRouter();

            $urlRoute = $router->build(  $url );
//            $urlRoute = $router::link( 'site' , $url );

            echo'<pre>';print_r( $urlRoute );echo'</pre>'.__FILE__.' '.__LINE__;
            die(__FILE__ .' '. __LINE__ );


            $urlStr= $urlRoute->toString();
            $dataArr = [
                'url' => $url ,
                'urlStr' => $urlStr,
                'categoryName' => $dataCategory['categoryName'],

            ];
             $returnArr[] = $dataArr  ;
//             $returnArr[] = JRoute::link('site', $url   )  ;
//            $returnArr[] = JRoute::_( $url , true, null, true )  ;
        }
//        echo'<pre>';print_r(  );echo'</pre>'.__FILE__.' '.__LINE__;
//        die(__FILE__ .' '. __LINE__ );
        return $returnArr ;


//        $Query->leftJoin('#__virtuemart_product_categories AS vmCatProd ON (vmCatProd.virtuemart_product_id = vmProd.virtuemart_product_id)');
//
//
//
//
//        foreach ( $productsResult as &$item)
//        {
//
//
//
//
//        }#END FOREACH
//        echo'<pre>';print_r( $productsResult );echo'</pre>'.__FILE__.' '.__LINE__;
//        die(__FILE__ .' '. __LINE__ );

    }

}