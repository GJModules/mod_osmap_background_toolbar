<?php

namespace OsmapBackgroundHelper;

use Exception;
use Joomla\CMS\Filesystem\File as JFile;
use Joomla\CMS\Language\LanguageHelper;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Language\Text;
use JUri;

class BackgroundComponent
{
	/**
	 * @var string Название компонента для которого создается карта
	 * @since 3.9
	 */
	protected $component;
	/**
	 * @var string - sef tag - обрабатываемого языка
	 * @since version
	 */
	protected $language ;
	/**
	 * @var array|null Массив с установленными языками -- или NULL - если Multilanguage - OFF
	 * @since version
	 */
	protected $languages = null ;
	/**
	 * @var int Счетчик для языков
	 * @since version
	 */
	protected $countLanguages = 0 ;
	/**
	 * Хранение тегов <url><loc>....<loc><url>
	 * @var string
	 * @since 3.9
	 */
	protected $urlLocTag = '' ;
	/**
	 * @var int Счетчик тегов <url><loc>....<loc><url>
	 * @since 3.9
	 */
	protected $urlLocCount = 0 ;
	/**
	 * Подпись для основного файла карты сайта
	 * @var string
	 * @since version
	 */
	protected $contextRootFiles = 'root' ;

	protected $params = [] ;
	/**
	 * @var \JDatabaseDriver|null
	 * @since version
	 */
	protected $db;
	/**
	 * @var string - Путь для сохранения файлов карты сайта
	 * @since 3.9
	 */
	protected $fileMapPath ;
	public function __construct()
	{
		$app          = \Joomla\CMS\Factory::getApplication();
		$this->params = $app->input->get('module_params' , [] , 'ARRAY' );

		// Get the active languages for multi-language sites
		if ( Multilanguage::isEnabled() ) {
			$this->languages = LanguageHelper::getLanguages();
			$this->language = mb_strtolower ( $this->languages[ $this->countLanguages ]->lang_code ) ;
			$this->language = str_replace( '-' , '_' ,  $this->language ) ;
		}
		$this->db = \JFactory::getDbo();
		// Устанавливаем директорию для сохранения
		$this->fileMapPath = $_SERVER['DOCUMENT_ROOT'];
	}

	/**
	 * Сбросить данные BackgroundComponent
	 * @return void -
	 * @since 3.9
	 */
	public function _reset(){
		$this->urlLocTag = '' ;
		$this->urlLocCount = 0 ;
	}

	/**
	 * Удаление файла /sitemap-com_* карты сайта
	 *
	 * @param   string  $name  Имя файла etc/ sitemap-com_filter_city-city-generator-custom-id-12.xml
	 * @param   bool    $updateMap
	 *
	 * @return bool|array
	 * @throws Exception
	 * @since 3.9
	 */
	protected function removeSiteMapComponent( string $name , bool $updateMap = false  )
	{
		$app = \Joomla\CMS\Factory::getApplication();

		$filePath = $this->fileMapPath.'/'.$name;
		JFile::delete($filePath);
		$app->enqueueMessage('Файл '. $name . ' удален.' );
		if ( $updateMap )
		{
			$resultData = $this->createFileAllMapXml();
			$this->_reset();
			return $resultData;
		}#END IF
		return true;
	}

	/**
	 * Создать основной файл карты сайта sitemap-root.xml
	 * @return string[]
	 * @throws Exception
	 * @since 3.9
	 */
    public function createFileAllMapXml(): array
    {
	    $app = \Joomla\CMS\Factory::getApplication();
	    $files    = glob($this->fileMapPath . '/sitemap-com*');

	    foreach ($files as &$file)
	    {
		    // получаем последний добавленный/измененный файл
		    $LastModified[$file] = filemtime($file); // массив файлов со временем изменения файла
		    $file = str_replace($this->fileMapPath, '', $file); // массив всех файлов
		    $this->addFileSitemapLoc($file);
	    }
	    $fileMapRoot    = $this->writeFileRootMap();

	    $fileMapRootURL = JUri::root() . $fileMapRoot;
		$UrlLink = '<a target="_blank" href="' . $fileMapRootURL . '">Перейти</a>' ;
	    $app->enqueueMessage('Основной файл карты сайта <b>'.$fileMapRootURL.'</b> создан. '. $UrlLink );

		$dataReselt = [
			'fileMapRootURL' => $fileMapRootURL,
			'LastModified' => $this->_getLastModifiedSiteMapFiles(),

		];

	    return $dataReselt ;
    }

	/**
	 * Получить данные о последней модификации файлов "/sitemap-com_*"
	 * @return array
	 * @since 3.9
	 */
	protected function _getLastModifiedSiteMapFiles():array
	{
		$LastModified = [] ;

		$files    = glob($this->fileMapPath . '/sitemap-com_*');
		foreach ($files as &$file)
		{
			$xmlFile = pathinfo( $file );
			$filename =  $xmlFile['basename'] ;
			$jdate = new \JDate(filemtime($file));
			$pretty_date = $jdate->format(Text::_('DATE_FORMAT_LC2'));

			// получаем последний добавленный/измененный файл
			$LastModified[$filename] = $pretty_date; // массив файлов со временем изменения файла
		}
		return $LastModified ;
	}

	/**
	 * Добавление ссылок  на файлы карт компонентов для файла sitemap-root.xml
	 * ect/ (sitemap-com_content-1.xml , sitemap-com_virtuemart-category-1.xml)
	 *
	 * @param $url
	 *
	 * @since version
	 */
	protected function addFileSitemapLoc( $url ){
		$url = preg_replace('/^\//' , '' , $url );
		$link = JUri::root().$url ;
		$this->urlLocTag .= '<sitemap>';
		$this->urlLocTag .=     '<loc>'.$link.'</loc>';
		$this->urlLocTag .= '</sitemap>';
	}

	/**
	 * Создать основной файл карты сайта со ссылками на подчиненные файлы
	 * @return string - имя файла
	 *
	 * @since version
	 */
	protected function writeFileRootMap(): string
	{
		$mapContent = '<?xml version="1.0" encoding="UTF-8"?>';
		$mapContent .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
		$mapContent .=      $this->urlLocTag ;
		$mapContent .= '</sitemapindex>';
		return $this->writeFile($this->contextRootFiles, false, $mapContent);
	}

	/**
	 * Записать ссылки в XML файл
	 *
	 * @param                $context
	 * @param   int|bool     $indexFile
	 * @param   string|bool  $mapContent
	 *
	 * @return string
	 *
	 * @since version
	 */
	public function writeFile( $context, $indexFile = false  , $mapContent = false ): string
	{
		// TODO - Изменить нижнее подчеркивание
		$this->component = str_replace('_' , '-' , $this->component ) ;
//		echo'<pre>';print_r( $this->component );echo'</pre>'.__FILE__.' '.__LINE__;
//		die(__FILE__ .' '. __LINE__ );

		if ( $indexFile )
		{
			$fileMapName = 'sitemap-' . $this->component . '-' . $context . '-' . $indexFile . '.xml';
		}else{
			$fileMapName = 'sitemap-' . $context . '.xml' ;
		}#END IF

		if ( $fileMapName == 'sitemap-root.xml' )
		{
//			$fileMapName = 'sitemap.xml' ;
		}#END IF
		
		if ( !$mapContent ) $mapContent = $this->urlLocTag ;  #END IF

		$pathFile    = JPATH_SITE . '/' . $fileMapName;
		try
		{
			// Code that may throw an Exception or Error.
			JFile::write($pathFile, $mapContent);

			return $fileMapName;
			// throw new \Exception('Code Exception '.__FILE__.':'.__LINE__) ;
		}
		catch ( Exception $e)
		{
			// Executed only in PHP 5, will not be reached in PHP 7
			echo 'Выброшено исключение: ', $e->getMessage(), "\n";
			echo '<pre>'; print_r($e); echo '</pre>' . __FILE__ . ' ' . __LINE__;
			die(__FILE__ . ' ' . __LINE__);
		}
	}


	/**
	 * Добавить ссылку в коллекцию для файла /sitemap-com_*
	 *
	 * @param         $url     - ссылка для добавления в <url><loc>
	 * @param   bool  $addRoot  - если TRUE - Добавить домен сайта
	 *
	 * @return void
	 * @since 3.9
	 */
    public function addUrlLocTag($url , bool $addRoot = true ){

	    $link = preg_replace('/^\//' , '' , $url );
		if ( $addRoot ) $link = JUri::root().$link ; #END IF

        $this->urlLocTag .= '<url>';
        $this->urlLocTag .=     '<loc>'.$link.'</loc>';
        $this->urlLocTag .= '</url>';
		$this->urlLocCount ++ ;
    }

	/**
	 * Запись в файл sitemap-com_...-{$context}-{$indexFile}.xml
	 *
	 * @param   bool|string  $context
	 * @param   bool|int         $indexFile
	 *
	 * @return string - Имя созданного файла XML
	 * @throws Exception
	 * @since 3.9
	 */
    protected function writeFileMap( $context = false , $indexFile = false  ):string
    {
		try
		{
			if ( !$context ) throw new Exception(' Переменная $context - пуста '.__FILE__.':'.__LINE__) ; #END IF

		}
		catch ( Exception $e)
		{
		    // Executed only in PHP 5, will not be reached in PHP 7
		    echo 'Выброшено исключение: ',  $e->getMessage(), "\n";
		    echo'<pre>';print_r( $e );echo'</pre>'.__FILE__.' '.__LINE__;
		    die(__FILE__ .' '. __LINE__ );
		}
	    if ( !$indexFile )
	    {
		    $app = \Joomla\CMS\Factory::getApplication();
		    // Номер файла
		    $indexFile = $app->input->get('indexFile' , 1 , 'INT');
	    }#END IF


		$mapContent = '<?xml version="1.0" encoding="UTF-8"?>';
        $mapContent .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        $mapContent .=      $this->urlLocTag ;
        $mapContent .= '</urlset>';
		// Очистить - хранение тегов
	    $this->urlLocTag = '';
	    return $this->writeFile($context, $indexFile, $mapContent);
    }

    /**
     * Поиск файла по маске
     * @param $pattern
     * @param $flags
     * @return array|false
     * @since 3.9
     */
    protected function search_file_by($pattern, $flags = 0)
    {

        // поиск по маске в папке
        $files = glob($pattern, $flags);
        foreach (glob(dirname($pattern) . '/*', GLOB_ONLYDIR | GLOB_NOSORT) as $dir)
        {

            // поиск в подпапках
            $files = array_merge($files, $this->search_file_by($dir . '/' . basename($pattern), $flags));
        }
        return $files;
    }

	/**
	 * Получить настройки модуля
	 * @return array
	 * @since 3.9
	 */
	protected static function getPluginSetting(){
		$plugin = \Joomla\CMS\Plugin\PluginHelper::getPlugin('osmap', 'com_virtuemart');
		$Registry = new \Joomla\Registry\Registry();
		return $Registry->loadObject( json_decode( $plugin->params ))->toArray();
	}

	/**
	 * Переставить на следующий язык - для Многоязычных сайтов
	 *
	 * @since version
	 */
	protected function changeLang(){
		$sefLang = $this->languages[ $this->countLanguages ]->sef ;
		$this->categoryResultMultilanguage[$sefLang] = $this->categoryResult ;
		$this->countLanguages ++ ;

		$this->language = mb_strtolower ( $this->languages[ $this->countLanguages ]->lang_code ) ;
		$this->language = str_replace( '-' , '_' ,  $this->language ) ;
	}
}